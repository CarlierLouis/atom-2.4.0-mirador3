<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

 /**
 * IIIF Viewer settings
 *
 * @package    AccesstoMemory
 * @subpackage settings
 * @author     Louis Carlier
 */

class SettingsIIIFViewerAction extends DefaultEditAction
{
  // Arrays not allowed in class constants
  public static
    $NAMES = array(
      'mirador',
    );

  protected function earlyExecute()
  {
    $this->i18n = sfContext::getInstance()->i18n;
  }

  protected function addField($name)
  {
    switch ($name)
    {
      case 'mirador':
        $this->miradorSetting = QubitSetting::getByName('iiifviewer_mirador');
        $default = 'no';
        $options = array(
          'no' => $this->i18n->__('No'),
          'yes' => $this->i18n->__('Yes'));

        $this->addSettingRadioButtonsField($this->miradorSetting, $name, $default, $options);

        break;
    }
  }

  private function addSettingRadioButtonsField($setting, $fieldName, $default, $options)
  {
    if (isset($setting))
    {
      $default = $setting->getValue(array('sourceCulture' => true));
    }

    $this->form->setDefault($fieldName, $default);
    $this->form->setValidator($fieldName, new sfValidatorString(array('required' => false)));
    $this->form->setWidget($fieldName, new sfWidgetFormSelectRadio(array('choices' => $options), array('class' => 'radio')));
  }


  protected function processField($field)
  {
    switch ($field->getName())
    {
      case 'mirador':
        $this->createOrUpdateSetting($this->miradorSetting, 'iiifviewer_mirador', $field->getValue());

        break;
    }
  }

  private function createOrUpdateSetting($setting, $name, $value)
  {
    if (!isset($setting))
    {
      $setting = new QubitSetting;
      $setting->name = $name;
      $setting->sourceCulture = 'en';
    }

    $setting->setValue($value, array('culture' => 'en'));
    $setting->save();
  }

  public function execute($request)
  {
    parent::execute($request);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getPostParameters());

      if ($this->form->isValid())
      {
        $this->processForm();

        QubitCache::getInstance()->removePattern('settings:i18n:*');

        $this->redirect(array('module' => 'settings', 'action' => 'iiifViewer'));
      }
    }
  }
}
