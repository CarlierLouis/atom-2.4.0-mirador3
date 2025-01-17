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
 * Display an 'IIIF manifest' digital asset
 *
 * @package    AccesstoMemory
 * @subpackage digital object
 * @author     Carlier Louis
 */
class DigitalObjectShowIIIFManifestComponent extends sfComponent
{   
  public function execute($request)
  {
    //https://unpkg.com/mirador@latest/dist/mirador.min.js
    //../node_modules/mirador/dist/mirador.min.js
    $this->response->addJavaScript('../node_modules/mirador/dist/mirador.min.js', 'last', ['async', 'defer']);
    $this->response->addJavaScript('mirador', 'last', ['async', 'defer']);
    $this->response->addStylesheet('mirador', 'last');
    $this->representation = $this->resource->getRepresentationByUsage($this->usageType);
  }
}