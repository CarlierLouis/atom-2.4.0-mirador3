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
 * @author Carlier Louis
 */ 

class MiradorUtils 
{
    public static function isJson($digitalObjectLink) 
    {
        $get_content = file_get_contents($digitalObjectLink);
        $json_data = json_decode($get_content);
        if ($json_data != null) 
        {
            return true;
        }
        else 
        {
            return false;
        }
    }

    public static function isIIIFManifest($digitalObjectLink)
    {
        $get_content = file_get_contents($digitalObjectLink);
        $json_data = json_decode($get_content, true);
        
        if ($json_data != null &&
            $json_data['@context'] === 'http://iiif.io/api/presentation/2/context.json' &&
            $json_data['@type'] === 'sc:Manifest' &&
            isset($json_data['@context']) &&
            isset($json_data['@id']) &&
            isset($json_data['@type'])
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function addParentChildrenToCatalog($digitalObjectLink, $resource) 
    {
        $catalog = [];
        if ($resource->parentId != 1) 
        {
            foreach ($resource->parent->getChildren() as $child) 
            {
                if ($resource->id != $child->id && self::isIIIFManifest($child->getDigitalObjectLink())) 
                {
                    $catalog[] = $child->getDigitalObjectLink();
                }
            }
        }
        return $catalog;
    }
}
?>