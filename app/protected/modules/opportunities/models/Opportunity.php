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

    class Opportunity extends OwnedSecurableItem
    {
        public static function getByName($name)
        {
            assert('is_string($name) && $name != ""');
            return self::getSubset(null, null, null, "name = '$name'");
        }

        public function __toString()
        {
            if (trim($this->name) == '')
            {
                return yii::t('Default', '(Unnamed)');
            }
            return $this->name;
        }

        public static function getModuleClassName()
        {
            return 'OpportunitiesModule';
        }

        public function untranslatedAttributeLabels()
        {
            return array_merge(parent::untranslatedAttributeLabels(), array(
                'account' => AccountsModule::getModuleLabelByTypeAndLanguage('Singular')));
        }

        /**
         * Returns the display name for the model class.
         * @return dynamic label name based on module.
         */
        protected static function getLabel()
        {
            return 'OpportunitiesModuleSingularLabel';
        }

        /**
         * Returns the display name for plural of the model class.
         * @return dynamic label name based on module.
         */
        protected static function getPluralLabel()
        {
            return 'OpportunitiesModulePluralLabel';
        }

        public static function canSaveMetadata()
        {
            return true;
        }

        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'name',
                    'closeDate',
                    'description',
                ),
                'relations' => array(
                    'account'       => array(RedBeanModel::HAS_ONE,   'Account'),
                    'stage'         => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED),
                    'source'        => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED),
                    'amount'        => array(RedBeanModel::HAS_ONE,   'CurrencyValue',    RedBeanModel::OWNED),
                    'contacts'      => array(RedBeanModel::MANY_MANY, 'Contact'),
                ),
                'rules' => array(
                    array('name',          'required'),
                    array('name',          'type',    'type' => 'string'),
                    array('name',          'length',  'min'  => 3, 'max' => 64),
                    array('stage',         'required'),
                    array('closeDate',     'required'),
                    array('closeDate',     'type', 'type' => 'date'),
                    array('amount',        'required'),
                    array('description',   'type',    'type' => 'string'),
                ),
                'elements' => array(
                    'amount'      => 'CurrencyValue',
                    'closeDate'   => 'Date',
                    'description' => 'TextArea',
                    'owner'       => 'User',
                    'account'     => 'Account',
                ),
                'customFields' => array(
                    'stage'  => 'SalesStages',
                    'source' => 'LeadSources',
                ),
                'defaultSortAttribute' => 'lastName',
                'rollupRelations' => array(
                    'contacts',
                ),
                'noAudit' => array(
                    'description'
                ),
            );
            return $metadata;
        }

        public static function isTypeDeletable()
        {
            return true;
        }

        public static function getRollUpRulesType()
        {
            return 'Opportunity';
        }

        public static function hasReadPermissionsOptimization()
        {
            return true;
        }
    }
?>