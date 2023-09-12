<?php
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockView;

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
                        print TimeclockView::getFormField($field);
                    endif;
                endforeach;
                ?>
