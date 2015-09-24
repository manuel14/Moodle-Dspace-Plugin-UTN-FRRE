<?php
/**
 * @package   repository_dspace
 * @copyright 2015, 
 * Bruno Demartino <demartinoba@gmail.com>
 * Leonardo Zaragoza <leozaragoza@gmail.com>
 * Marcelo Espinoza <marceloespinoza00@gmail.com>
 * Manuel Zubieta <manuelzubieta14@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$capabilities = array(
    'repository/dspace:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'user' => CAP_ALLOW
        )
    )
);