<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2011 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
     * details.
     *
     * You should have received a copy of the GNU General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 113 McHenry Road Suite 207,
     * Buffalo Grove, IL 60089, USA. or at email address contact@zurmo.com.
     ********************************************************************************/

    class ApiRestUserTest extends ApiRestTest
    {
        public function testApiServerUrl()
        {
            $this->assertTrue(strlen($this->serverUrl) > 0);
        }

        /**
        * @depends testApiServerUrl
        */
        public function testGetUser()
        {
            Yii::app()->user->userModel        = User::getByUsername('super');
            $super = User::getByUsername('super');
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $user = UserTestHelper::createBasicUser('PeterSmith');

            $redBeanModelToApiDataUtil  = new RedBeanModelToApiDataUtil($user);
            $compareData  = $redBeanModelToApiDataUtil->getData();

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals($compareData, $response['data']);
        }

        /**
         * @depends testGetUser
         */
        public function testDeleteUser()
        {
            Yii::app()->user->userModel        = User::getByUsername('super');
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $user = User::getByUsername('petersmith');

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'DELETE', $headers);

            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('The id specified was invalid.', $response['message']);
        }

        public function testCreateUser()
        {
            Yii::app()->user->userModel        = User::getByUsername('super');
            $super = User::getByUsername('super');
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $titles = array('Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Swami');
            $customFieldData = CustomFieldData::getByName('Titles');
            $customFieldData->serializedData = serialize($titles);
            $this->assertTrue($customFieldData->save());

            $primaryEmail['emailAddress']   = "a@example.com";
            $primaryEmail['optOut']         = 1;

            $primaryAddress['street1']      = '129 Noodle Boulevard';
            $primaryAddress['street2']      = 'Apartment 6000A';
            $primaryAddress['city']         = 'Noodleville';
            $primaryAddress['postalCode']   = '23453';
            $primaryAddress['country']      = 'The Good Old US of A';

            $currencies                 = Currency::getAll();
            $currency    = $currencies[0];
            $this->assertEquals('USD', $currency->code);

            $manager = new User();
            $manager->username     = 'smith45';
            $manager->title->value = 'Mr.';
            $manager->firstName    = 'Super';
            $manager->lastName     = 'User';
            $manager->setPassword('smith45');
            $saved = $manager->save();
            $this->assertTrue($saved);

            $data['firstName']           = "Michael";
            $data['lastName']            = "Smith";
            $data['jobTitle']            = "President";
            $data['department']          = "Sales";
            $data['officePhone']         = "653-235-7824";
            $data['mobilePhone']         = "653-235-7821";
            $data['officeFax']           = "653-235-7834";
            $data['username']            = "diggy011";
            $data['password']            = "diggy011";
            $data['language']            = "en";
            $data['timeZone']            = "America/Chicago";

            $data['title']['value']      = $titles[3];
            $data['manager']['id']       = $manager->id;

            $data['primaryEmail']        = $primaryEmail;
            $data['primaryAddress']      = $primaryAddress;

            $data['currency']       = array(
                'id' => $currency->id
            );

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);

            $data['createdByUser']    = array(
                'id' => $super->id,
                'username' => 'super'
            );
            $data['modifiedByUser'] = array(
                'id' => $super->id,
                'username' => 'super'
            );

            // We need to unset some empty values from response.
            unset($response['data']['createdDateTime']);
            unset($response['data']['modifiedDateTime']);
            unset($response['data']['primaryEmail']['id'] );
            unset($response['data']['primaryEmail']['isInvalid'] );
            unset($response['data']['primaryAddress']['id']);
            unset($response['data']['primaryAddress']['state']);
            unset($response['data']['primaryAddress']['longitude']);
            unset($response['data']['primaryAddress']['latitude']);
            unset($response['data']['primaryAddress']['invalid']);
            unset($response['data']['title']['id']);
            unset($response['data']['id']);
            unset($response['data']['password']);
            unset($response['data']['hash']);
            unset($response['data']['manager']['username']);
            unset($data['password']);
            ksort($data);
            ksort($response['data']);
            $this->assertEquals($data, $response['data']);
            // Check if new user can log in
            $newUser = User::getByUsername('diggy011');
            $newUser->setRight('UsersModule', UsersModule::RIGHT_LOGIN_VIA_WEB_API);
            $saved = $newUser->save();
            $authenticationData = $this->login('diggy011', 'diggy011');
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );


            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
        }

        /**
         * @depends testCreateUser
         */
        public function testUpdateUser(){
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $user = User::getByUsername('diggy011');

            $redBeanModelToApiDataUtil  = new RedBeanModelToApiDataUtil($user);
            $compareData  = $redBeanModelToApiDataUtil->getData();

            $data['firstName']                = "John";
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);

            // We need to unset some empty values from response and dates.
            unset($response['data']['modifiedDateTime']);
            unset($compareData['modifiedDateTime']);
            $compareData['firstName'] = "John";
            ksort($compareData);
            ksort($response['data']);
            $this->assertEquals($compareData, $response['data']);

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            unset($response['data']['modifiedDateTime']);
            ksort($response['data']);
            $this->assertEquals($compareData, $response['data']);
        }

        /**
         * @depends testUpdateUser
         */
        public function testListUsers()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/', 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(3, count($response['data']['array']));
        }


        /**
         * @depends testListUsers
         */
        public function testUnprivilegedUserViewUpdateDeleteUsers()
        {
            Yii::app()->user->userModel        = User::getByUsername('super');
            $notAllowedUser = UserTestHelper::createBasicUser('Steven');
            $notAllowedUser->setRight('UsersModule', UsersModule::RIGHT_LOGIN_VIA_WEB_API);
            $saved = $notAllowedUser->save();

            $authenticationData = $this->login('steven', 'steven');
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $user = User::getByUsername('diggy011');
            $data['firstName']                = "Sam";

            // Test with unprivileged user to view, edit and delete account.
            $authenticationData = $this->login('steven', 'steven');
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('You do not have rights for this action.', $response['message']);

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('You do not have rights for this action.', $response['message']);

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'DELETE', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('You do not have rights for this action.', $response['message']);

            // Test with privileged user
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            //Test Delete
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'DELETE', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $user->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
        }

        /**
        * @depends testUnprivilegedUserViewUpdateDeleteUsers
        */
        public function testSearchUsers()
        {
            Yii::app()->user->userModel        = User::getByUsername('super');
            $super = User::getByUsername('super');
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $manager = User::getByUsername('smith45');

            UserTestHelper::createBasicUser('First');
            UserTestHelper::createBasicUser('Second');
            UserTestHelper::createBasicUser('Third');
            UserTestHelper::createBasicUser('Forth');
            UserTestHelper::createBasicUserWithManager('Fifth', $manager);

            $searchParams = array(
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 3,
                ),
                'search' => array(
                    'username' => '',
                ),
                'sort' => 'username',
            );
            $searchParamsQuery = http_build_query($searchParams);
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(3, count($response['data']['array']));
            $this->assertEquals(8, $response['data']['total']);
            $this->assertEquals('fifth', $response['data']['array'][0]['username']);
            $this->assertEquals('first', $response['data']['array'][1]['username']);
            $this->assertEquals('forth', $response['data']['array'][2]['username']);

            // Second page
            $searchParams['pagination']['page'] = 2;
            $searchParamsQuery = http_build_query($searchParams);
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(3, count($response['data']['array']));
            $this->assertEquals(8, $response['data']['total']);
            $this->assertEquals('second', $response['data']['array'][0]['username']);
            $this->assertEquals('smith45', $response['data']['array'][1]['username']);
            $this->assertEquals('steven', $response['data']['array'][2]['username']);

            // Search by name
            $searchParams['pagination']['page'] = 1;
            $searchParams['search']['username'] = 'first';
            $searchParamsQuery = http_build_query($searchParams);
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(1, count($response['data']['array']));
            $this->assertEquals(1, $response['data']['total']);
            $this->assertEquals('first', $response['data']['array'][0]['username']);

            // No results
            $searchParams['pagination']['page'] = 1;
            $searchParams['search']['username'] = 'first2';
            $searchParamsQuery = http_build_query($searchParams);
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(0, $response['data']['total']);
            $this->assertFalse(isset($response['data']['array']));

            // Search by name desc.
            $searchParams = array(
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 3,
                ),
                'search' => array(
                     'username' => '',
                ),
                'sort' => 'username.desc',
            );
            $searchParamsQuery = http_build_query($searchParams);
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(3, count($response['data']['array']));
            $this->assertEquals(8, $response['data']['total']);
            $this->assertEquals('third', $response['data']['array'][0]['username']);
            $this->assertEquals('super', $response['data']['array'][1]['username']);
            $this->assertEquals('steven', $response['data']['array'][2]['username']);

            // Second page
            $searchParams['pagination']['page'] = 2;
            $searchParamsQuery = http_build_query($searchParams);
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(3, count($response['data']['array']));
            $this->assertEquals(8, $response['data']['total']);
            $this->assertEquals('smith45', $response['data']['array'][0]['username']);
            $this->assertEquals('second', $response['data']['array'][1]['username']);
            $this->assertEquals('forth', $response['data']['array'][2]['username']);

            // Search by custom fields, order by name desc
            $searchParams = array(
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 3,
                ),
                'search' => array(
                    'manager'   => array( 'id' => $manager->id),
                ),
                'sort' => 'username',
            );
            $searchParamsQuery = http_build_query($searchParams);
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(1, count($response['data']['array']));
            $this->assertEquals(1, $response['data']['total']);
            $this->assertEquals('fifth', $response['data']['array'][0]['username']);
        }

        public function testEditUserWithIncompleteData()
        {
            Yii::app()->user->userModel        = User::getByUsername('super');
            $super = User::getByUsername('super');
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $user = UserTestHelper::createBasicUser('PeterSmith');

            // Provide data without required fields.
            $data['username']         = "";

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals(2, count($response['errors']));

            $id = $user->id;
            $data = array();
            $data['username']                = '';
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals(1, count($response['errors']));
        }

        public function testEditUserWIthIncorrectDataType()
        {
            Yii::app()->user->userModel        = User::getByUsername('super');
            $super = User::getByUsername('super');
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $user = UserTestHelper::createBasicUser('JosephSmith');

            // Provide data with wrong type.
            $data['language']         = "AAAA";

            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals(3, count($response['errors']));

            $id = $user->id;
            $data = array();
            $data['language']         = "AAAAA";
            $response = ApiRestTestHelper::createApiCall($this->serverUrl . '/test.php/users/api/' . $id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals(1, count($response['errors']));
        }
    }
?>