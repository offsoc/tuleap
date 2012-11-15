<?php
/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'ConditionsCollection.class.php';
require_once 'Condition/Permissions/Factory.class.php';
require_once 'Condition/FieldNotEmpty/Factory.class.php';
require_once TRACKER_BASE_DIR .'/workflow/Transition.class.php';
require_once TRACKER_BASE_DIR .'/workflow/Transition/Condition/FieldNotEmpty/Dao.class.php';

class Workflow_Transition_ConditionFactory {

    /** @var Workflow_Transition_Condition_Permissions_Factory */
    private $permissions_factory;

    /** @var Workflow_Transition_Condition_FieldNotEmpty_Factory */
    private $fieldnotempty_factory;

    /**
     * Should use the build() method
     */
    public function __construct(
        Workflow_Transition_Condition_Permissions_Factory $permissions_factory,
        Workflow_Transition_Condition_FieldNotEmpty_Factory $fieldnotempty_factory
    ) {
        $this->permissions_factory   = $permissions_factory;
        $this->fieldnotempty_factory = $fieldnotempty_factory;
    }

    /**
     * @return Workflow_Transition_ConditionFactory
     */
    public static function build() {
        return new Workflow_Transition_ConditionFactory(
            new Workflow_Transition_Condition_Permissions_Factory(),
            new Workflow_Transition_Condition_FieldNotEmpty_Factory(new Workflow_Transition_Condition_FieldNotEmpty_Dao())
        );
    }

    /**
     * @return Workflow_Transition_ConditionsCollection
     */
    public function getConditions(Transition $transition) {
        $collection = new Workflow_Transition_ConditionsCollection();
        $collection->add(new Workflow_Transition_Condition_Permissions($transition));
        $collection->add($this->getFieldNotEmpty($transition));
        return $collection;
    }

    private function getFieldNotEmpty(Transition $transition){
        return $this->fieldnotempty_factory->getFieldNotEmpty($transition);
    }

    private function getTransition($transition_id) {
        $transition_factory = TransitionFactory::instance();
        return $transition_factory->getTransition($transition_id);
    }

    /**
     * Deletes all exiting conditions then saves the new condition.
     * @param Transition $transition
     * @param int $field_id
     * @return int The ID of the newly created condition
     */
    public function addCondition($transition, $field_id) {
        $this->getFieldNotEmptyDao()->deleteByTransitionId($transition->getId());
        if ($field_id) {
            return $this->getFieldNotEmptyDao()->create($transition->getId(), $field_id);
        }
    }

    private function getFieldNotEmptyDao() {
        return new Workflow_Transition_Condition_FieldNotEmpty_Dao();
    }

    /**
     * Create all conditions on a transition from a XML
     *
     * @return Workflow_Transition_ConditionsCollection
     */
    public function getAllInstancesFromXML($xml, &$xmlMapping, Transition $transition) {
        $conditions = new Workflow_Transition_ConditionsCollection();
        if ($this->isLegacyXML($xml)) {
            if ($xml->permissions) {
                $conditions->add($this->permissions_factory->getInstanceFromXML($xml->permissions, $xmlMapping, $transition));
            }
        } else if ($xml->conditions) {
            foreach ($xml->conditions->condition as $xml_condition) {
                $conditions->add($this->getInstanceFromXML($xml_condition, $xmlMapping, $transition));
            }
        }
        return $conditions;
    }

    /**
     * Creates a transition Object
     *
     * @param SimpleXMLElement $xml         containing the structure of the imported workflow
     * @param array            &$xmlMapping containig the newly created formElements idexed by their XML IDs
     *
     * @return Workflow_Transition_Condition The condition object, or null if error
     */
    private function getInstanceFromXML($xml, &$xmlMapping, Transition $transition) {
        $type      = (string)$xml['type'];
        $condition = null;
        switch ($type) {
            case 'perms':
                if ($xml->permissions) {
                    $condition = $this->permissions_factory->getInstanceFromXML($xml, $xmlMapping, $transition);
                }
                break;
            case 'notempty':
                $condition = $this->fieldnotempty_factory->getInstanceFromXML($xml, $xmlMapping, $transition);
                break;
        }
        return $condition;
    }

    /**
     * Say if we are using a deprecated xml file.
     *
     * Before Tuleap 5.7, permissions element was located here:
     *
     * <transition>
     *   ...
     *   <permissions>
     *     ...
     *   </permissions>
     * </transition>
     *
     * instead of:
     *
     * <transition>
     *   ...
     *   <conditions>
     *     <condition type="perm">
     *       <permissions>
     *         ...
     *       </permissions>
     *     </condition>
     *   </conditions>
     * </transition>
     *
     * @see getInstanceFromXML
     *
     * @return bool
     */
    private function isLegacyXML($xml) {
        return isset($xml->permissions);
    }

    public function duplicate($from_transition_id, $transition_id, $field_mapping, $ugroup_mapping, $duplicate_type) {
        $this->duplicatePermissions($from_transition_id, $transition_id, $ugroup_mapping, $duplicate_type);
        $transition = $this->getTransition($from_transition_id);
        $transition_field = $this->getFieldNotEmpty($transition);
        if ($transition_field->getFieldId()) {
            echo ($from_transition_id);
            $this->duplicateFieldNotEmpty($from_transition_id, $transition_id, $field_mapping);
        }
    }

    /**
    * Duplicate the transitions permissions
    *
    * @param int $from_transition_id the old transition id
    * @param int $transition_id the new transition id
    * @param Array $ugroup_mapping the ugroup mapping
    *
    * @return void
    */
    private function duplicatePermissions($from_transition_id, $transition_id, $ugroup_mapping, $duplicate_type) {
        $pm = PermissionsManager::instance();
        $permission_type = array('PLUGIN_TRACKER_WORKFLOW_TRANSITION');
        //Duplicate tracker permissions
        $pm->duplicatePermissions($from_transition_id, $transition_id, $permission_type, $ugroup_mapping, $duplicate_type);
    }

        /**
    * Duplicate the transitions permissions
    *
    * @param int $from_transition_id the old transition id
    * @param int $transition_id the new transition id
    * @param Array $ugroup_mapping the ugroup mapping
    *
    * @return void
    */
    private function duplicateFieldNotEmpty($from_transition_id, $transition_id, $field_mapping) {
        $dao = new Workflow_Transition_Condition_FieldNotEmpty_Dao();
        //Duplicate
        $dao->duplicate($from_transition_id, $transition_id, $field_mapping);
    }
}
?>
