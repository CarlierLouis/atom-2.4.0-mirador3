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

class drmcTweaksTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'qubit'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'cli'),
    ));

    $this->namespace        = 'drmc';
    $this->name             = 'tweaks';
    $this->briefDescription = 'Tweak DRMC AIP data';
    $this->detailedDescription = <<<EOF
Tweak DRMC data
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    sfContext::createInstance($this->configuration);

    // get appropriate info objects
    $criteria = new Criteria;
    $criteria->add(QubitInformationObject::LEVEL_OF_DESCRIPTION_ID, sfConfig::get('app_drmc_lod_artwork_record_id'));
    $items = QubitInformationObject::get($criteria);

    // add random collection dates
    foreach($items as $item) {
      // add random collection date to information object
      $random = mt_rand(1262055681, 1399488461);
      $randomDate = date("Y-m-d", $random);
      $item->addProperty('Dated', $randomDate);
      $item->save();

      // add random byte size to associated digital object
      $criteria = new Criteria;
      $criteria->add(QubitDigitalObject::INFORMATION_OBJECT_ID, $item->id);
      $do = QubitDigitalObject::getOne($criteria);

      // if digital object doesn't exist, make one with random size
      if (!$do)
      {
        $do = new QubitDigitalObject;
        $do->informationObject = $item;
        $do->byteSize = rand(1000, 10000000);
        $do->save();
      }

      print '.';
    }

    print "\nTweaks made.\n";
  }
}
