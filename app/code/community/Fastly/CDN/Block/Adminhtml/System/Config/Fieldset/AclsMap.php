<?php
/**
 * Fastly CDN for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Fastly CDN for Magento End User License Agreement
 * that is bundled with this package in the file LICENSE_FASTLY_CDN.txt.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Fastly CDN to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fastly
 * @package     Fastly_CDN
 * @copyright   Copyright (c) 2015 Fastly, Inc. (http://www.fastly.com)
 * @license     BSD, see LICENSE_FASTLY_CDN.txt
 */

class Fastly_CDN_Block_Adminhtml_System_Config_Fieldset_AclsMap
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_addRowButtonHtml = array();
    protected $_removeRowButtonHtml = array();
    protected $_editRowButtonHtml = array();

    /**
     * Returns HTML snippet to select geo IP cms block
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $html = '<table id="fastlycdn_acl_cmsblock_template" style="display:none;">';
        $html .= $this->_getRowTemplateHtml(-1);
        $html .= '</table>';

        $html .= '<div class="grid">';
        $html .= '<table class="border" cellpadding="0" cellspacing="0" id="fastlycdn_acl_cmsblock">';

        $html .= '<tr class="headings">';
        $html .= '<th>'.$this->__('Name').'</th>';
        $html .= '<th>&nbsp;</th>';
        $html .= '</tr>';


        $acls = $this->getAcls();

        if ($acls) {
            foreach ($acls as $acl) {
                $html .= $this->_getRowTemplateHtml($acl);
            }
        }
        $html .= '</table>';
        $html .= $this->_getAddRowButtonHtml('fastlycdn_acl_cmsblock',
            'fastlycdn_acl_cmsblock_template', $this->__('Add acl'));
        $html .= '</div>';

        return $html;
    }

    /**
     * Return row to add new cms block
     *
     * @param object $acl
     * @return string
     */
    protected function _getRowTemplateHtml($acl)
    {
        if($acl != -1) {
            $name = $acl->name;
            $id = $acl->id;
            $html = '<tr id="'. $name .'">';
        } else {
            $name = '';
            $id = '';
            $html = '<tr>';
        }

        $html .= '<td>';
        $html .= '<input type="text" name="" style="width:100px" value="'.$name.'" disabled/>';
        $html .= '</td>';

        $html .= '<td>';
        $html .= $this->_getEditRowButtonHtml($id);
        $html .= '</td>';

        $html .= '<td>';
        $html .= $this->_getRemoveRowButtonHtml($name);
        $html .= '</td>';

        $html .= '</tr>';

        return $html;
    }

    /**
     * Returns available cms blocks
     *
     * @return mixed
     */
    protected function _getCmsBlocks()
    {
        return Mage::getResourceModel('cms/block_collection')->load()->toOptionArray();
    }

    /**
     * check if element is disabled
     *
     * @return string
     */
    protected function _getDisabled()
    {
        return $this->getElement()->getDisabled() ? ' disabled' : '';
    }

    protected function _getValue($key)
    {
        return $this->getElement()->getData('value/'.$key);
    }

    /**
     * Returns id an entry is selected
     *
     * @param $key
     * @param $value
     * @return string
     */
    protected function _getSelected($key, $value)
    {
        return $this->getElement()->getData('value/'.$key)==$value ? 'selected="selected"' : '';
    }

    /**
     * Returns buttons to add new entry
     *
     * @param $container
     * @param $template
     * @param string $title
     * @return mixed
     */
    protected function _getAddRowButtonHtml($container, $template, $title='Add')
    {
        if (!isset($this->_addRowButtonHtml[$container])) {
            $this->_addRowButtonHtml[$container] = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setType('button')
                ->setClass('add '.$this->_getDisabled())
                ->setLabel($this->__($title))
                ->setOnClick("Fastly.initDialog('acl-create-form'); return false;")
                ->setDisabled($this->_getDisabled())
                ->toHtml();
        }
        return $this->_addRowButtonHtml[$container];
    }

    /**
     * Returns button to edit an entry
     *
     * @param string $aclId
     * @return array
     */
    protected function _getEditRowButtonHtml($aclId)
    {
        $onClick = "Fastly.initDialog('acl-list-form', '" . $aclId . "')";
        $this->_editRowButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel($this->__('Manage Items'))
            ->setOnClick($onClick)
            ->setDisabled($this->_getDisabled())
            ->toHtml();
        return $this->_editRowButtonHtml;
    }

    /**
     * Returns button to remove an entry
     *
     * @param string $name
     * @return array
     */
    protected function _getRemoveRowButtonHtml($name)
    {
        $onClick = "Fastly.initDialog('acl-remove-form', '" . $name . "', $(this).up('tr'))";
        $this->_removeRowButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('delete v-middle '.$this->_getDisabled())
            ->setLabel($this->__('Delete'))
            ->setOnClick($onClick)
            ->setDisabled($this->_getDisabled())
            ->toHtml();
        return $this->_removeRowButtonHtml;
    }

    public function getAcls()
    {
        $control = Mage::getModel('fastlycdn/control');
        $service = $control->checkServiceDetails();
        if(!is_array($service->versions)) {
            return false;
        }
        $currActiveVersion = Mage::helper('fastlycdn')->determineVersions($service->versions);

        $acls = $control->getAcls($currActiveVersion['active_version']);

        return $acls;
    }
}
