<?php
/**
 * @author Tahu Szilárd
 */
namespace App\Interfaces;
 
interface PageInterface
{
    static function head();
 
    static function nav();
 
    static  function footer();
 
    static function tableHead();
 
    static function tableBody(array $entities);
 
    static function table(array $entities);
 
    static function searchbar();
}