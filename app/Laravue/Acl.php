<?php
/**
 * File Acl.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */
namespace App\Laravue;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class Acl
 *
 * @package App\Laravue
 */
final class Acl
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_EDITOR = 'editor';
    const ROLE_USER = 'user';
    const ROLE_VISITOR = 'visitor';

    const ROLE_CRU_VIEWER = 'cru_viewer';
    const ROLE_SEVRU_VIEWER = 'sevru_viewer';
    const ROLE_YURU_VIEWER = 'yuru_viewer';
    const ROLE_GMZ1_VIEWER = 'gmz1_viewer';
    const ROLE_MAIN_VIEWER = 'main_viewer';

    const ROLE_CRU_EDITOR = 'cru_editor';
    const ROLE_SEVRU_EDITOR = 'sevru_editor';
    const ROLE_YURU_EDITOR = 'yuru_editor';
    const ROLE_GMZ1_EDITOR = 'gmz1_editor';
    const ROLE_MAIN_EDITOR = 'main_editor';

    const PERMISSION_VIEW_MENU_ELEMENT_UI = 'view menu element ui';
    const PERMISSION_VIEW_MENU_PERMISSION = 'view menu permission';
    const PERMISSION_VIEW_MENU_COMPONENTS = 'view menu components';
    const PERMISSION_VIEW_MENU_CHARTS = 'view menu charts';
    const PERMISSION_VIEW_MENU_NESTED_ROUTES = 'view menu nested routes';
    const PERMISSION_VIEW_MENU_TABLE = 'view menu table';
    const PERMISSION_VIEW_MENU_ADMINISTRATOR = 'view menu administrator';
    const PERMISSION_VIEW_MENU_THEME = 'view menu theme';
    const PERMISSION_VIEW_MENU_CLIPBOARD = 'view menu clipboard';
    const PERMISSION_VIEW_MENU_EXCEL = 'view menu excel';
    const PERMISSION_VIEW_MENU_ZIP = 'view menu zip';
    const PERMISSION_VIEW_MENU_PDF = 'view menu pdf';
    const PERMISSION_VIEW_MENU_I18N = 'view menu i18n';

    const PERMISSION_VIEW_MENU_CRU = 'view menu cru';
    const PERMISSION_VIEW_MENU_SEVRU = 'view menu sevru';
    const PERMISSION_VIEW_MENU_YURU = 'view menu yuru';
    const PERMISSION_VIEW_MENU_GMZ1 = 'view menu gmz1';
    const PERMISSION_VIEW_MENU_MAIN = 'view menu main';
    const PERMISSION_VIEW_MENU_EXCAVATORS = 'view menu excavators';
    const PERMISSION_VIEW_MENU_DRILLS = 'view menu drills';

    const PERMISSION_USER_MANAGE = 'manage user';
    const PERMISSION_ARTICLE_MANAGE = 'manage article';
    const PERMISSION_PERMISSION_MANAGE = 'manage permission';

    const PERMISSION_EXCAVATORS_MANAGE = 'manage excavators';
    const PERMISSION_DRILLS_MANAGE = 'manage drills';

    /**
     * @param array $exclusives Exclude some permissions from the list
     * @return array
     */
    public static function permissions(array $exclusives = []): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) use ($exclusives) {
                return !in_array($value, $exclusives) && Str::startsWith($key, 'PERMISSION_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    public static function menuPermissions(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'PERMISSION_VIEW_MENU_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    /**
     * @return array
     */
    public static function roles(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $roles =  Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'ROLE_');
            });

            return array_values($roles);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }
}
