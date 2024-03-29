<?php
use HUGLLC\Component\Timeclock\Administrator\Helper\ViewHelper;

            $fieldset = $displayData["form"]->getFieldset($displayData["name"]);
                // Iterate through the fields and display them.
                foreach($fieldset as $field):
                    $name = $field->name;
                    if (isset($displayData["data"]->$name)) {
                        $field->setValue($displayData["data"]->$name);
                    }
                    // If the field is hidden, only use the input.
                    if ($field->hidden):
                        echo $field->input;
                    else:
                        print ViewHelper::getFormField($field);
                    endif;
                endforeach;
                ?>
