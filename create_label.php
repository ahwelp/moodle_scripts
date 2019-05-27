<?php

//Load the config file. It might be in a very diferent place
require_once('../../../config.php');

die();

// Load course lib
require_once($CFG->dirroot . '/course/lib.php');

// You may find intresting create a new topic
//$new_section = course_create_section($course);

function createLabel($content = '', $course = 0, $topicnum = 0, $properties = Array()){

    global $DB;

    $module = $DB->get_record('modules', Array('name'=>'label'));

    //Fill the necessary data for topic
    $label = new stdClass();
    $label->name = substr($content, 0, 50);
    $label->intro = $content;
    $label->course = $course;
    $label->timemodified = time();
    $label->introformat = 1;

    $label->id = $DB->insert_record('label', $label, true);

    //Load The Topic
    $section = $DB->get_record_sql("SELECT * 
                                     FROM {course_sections} 
                                    WHERE course = ? 
                                     OFFSET ?",
                                   Array($course, $topicnum) );

    //Fill the necessary data for the section
    $course_modules = new stdClass();
    $course_modules->course = $course;
    $course_modules->module = $module->id;
    $course_modules->instance = $label->id;
    $course_modules->section = $section->id;
    $course_modules->added = time();

    //Fill the necessary config for the section
    $course_modules->score = (isset($properties['score'])) ? $properties['score'] : 0;
    $course_modules->indent = (isset($properties['indent'])) ? $properties['indent'] : 0;
    $course_modules->visible = (isset($properties['visible'])) ? $properties['visible'] : 1;
    $course_modules->visibleold = (isset($properties['visibleold'])) ? $properties['visibleold'] : 1;
    $course_modules->groupmode = (isset($properties['groupmode'])) ? $properties['groupmode'] : 0;
    $course_modules->groupingid = (isset($properties['groupingid'])) ? $properties['groupingid'] : 0;
    $course_modules->completion = (isset($properties['completion'])) ? $properties['completion'] : 0;
    $course_modules->completiongradeitemnumber = (isset($properties['completiongradeitemnumber'])) ? $properties['completiongradeitemnumber'] : 0;
    $course_modules->completionview = (isset($properties['completionview'])) ? $properties['completionview'] : 0;
    $course_modules->completionexpected = (isset($properties['completionexpected'])) ? $properties['completionexpected'] : 0;
    $course_modules->showdescription = (isset($properties['showdescription'])) ? $properties['showdescription'] : 0;
    $course_modules->availability = (isset($properties['availability'])) ? $properties['availability'] : 0;
    $course_modules->deletioninprogress = (isset($properties['deletioninprogress'])) ? $properties['deletioninprogress'] : 0;
    $course_modules->visibleoncoursepage = (isset($properties['visibleoncoursepage'])) ? $properties['visibleoncoursepage'] : 1;

    $course_modules->id = $DB->insert_record('course_modules', $course_modules, true);
    //Add the new label to the list of ids on the sequence
    $section->sequence .= ','.$course_modules->id;

    $DB->update_record('course_sections', $section);

    //Clear cache, so the label can appear
    rebuild_course_cache($course, true);

}

createLabel($content, $course, $section_num, $properties);