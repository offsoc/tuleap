<?php
/**
 * Copyright (c) STMicroelectronics, 2010. All Rights Reserved.
 *
 * This file is a part of Codendi.
 *
 * Codendi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License.
 *
 * Codendi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codendi. If not, see <http://www.gnu.org/licenses/>.
 */
require_once(CODENDI_CLI_DIR.'/CLI_Action.class.php');

class CLI_Action_Docman_GetFile extends CLI_Action {
    function CLI_Action_Docman_GetFile() {
        $this->CLI_Action('getFile', 'Returns the file content.');
        $this->setSoapCommand('getDocmanFileChunk');
            $this->addParam(array(
            'name'           => 'item_id',
            'description'    => '--id=<item_id>     ID of the item we want to download',
            'parameters'     => array('id'),
        ));
        $this->addParam(array(
            'name'           => 'version',
            'description'    => '--version_number=<version>     (Optional) The version that we want to download, if not specified the current one'
        ));
        $this->addParam(array(
            'name'           => 'output',
            'description'    => '--output=<location>          (Optional) Name of the file to write the file to',
        ));

    }
    
    function validate_item_id(&$item_id) {
        if (!$item_id) {
            exit_error("You must specify the ID of the document with the --id parameter");
        }
        return true;
    }

    function validate_output(&$output) {
        if ($output) {
            $output = trim($output);
            if ($output && file_exists($output)) {
                if (!$this->user_confirm("File $output already exists. Do you want to overwrite it?")) {
                    exit_error("Retrieval of file aborted");
                }
            }
        }
        return true;
    }
    
    function soapCall($soap_params, $use_extra_params = true) {
        // Prepare SOAP parameters
        $callParams = $soap_params;
        unset($callParams['output']);
        $callParams['chunk_offset']     = 0;
        $callParams['chunk_size'] = $GLOBALS['soap']->getFileChunkSize();

        // Manage screen/file output
        $output = false;
        if ($soap_params['output']) {
            $output = $soap_params['output'];
        }
        if ($output !== false) {
            while (!($fd = @fopen($output, "wb"))) {
                echo "Couldn't open file ".$output." for writing.\n";
                $output = "";
                while (!$output) {
                    $output = get_user_input("Please specify a new file name: ");
                }
            }
        }

        $startTime = microtime(true);
        $totalTran = 0;
        $i = 0;
        do {
            $callParams['chunk_offset'] = $i * $GLOBALS['soap']->getFileChunkSize();
            $content = base64_decode($GLOBALS['soap']->call($this->soapCommand, $callParams, $use_extra_params));
            $cLength = strlen($content);
            if ($output !== false) {
                $written = fwrite($fd, $content);
                if ($written != $cLength) {
                    throw new Exception('Received '.$cLength.' of data but only '.$written.' written on Disk');
                }
            } else {
                echo $content;
            }
            $totalTran += $cLength;
            $i++;
        } while ($cLength >= $GLOBALS['soap']->getFileChunkSize());
        $endTime = microtime(true);

        $transRate = $totalTran / ($endTime - $startTime);
        $GLOBALS['LOG']->add('Transfer rate: '.size_readable($transRate, null, 'bi', '%.2f %s/s'));
        // Finish!
        echo "\rSending file (100%)\n";

        if ($output !== false) {
            fclose($fd);

            unset($callParams['chunk_offset']);
            unset($callParams['chunk_size']);
            
            //Check the md5sum
            $localChecksum = md5_file($output);
            $remoteChecksum = $GLOBALS['soap']->call('getDocmanFileMD5sum', $callParams, $use_extra_params);
            if ($localChecksum == $remoteChecksum) {
                echo "File downloaded successfully\n";
            } else {
                echo "ERROR: Local and remote checksums are not the same. Try to download it again.\n";
            }
        }
    }
}

?>