<?php

namespace App\Helpers;

use App\Models\User;

class AdminHelper
{
    /**
     * Get admin identity data
     */
    public static function getAdminIdentity()
    {
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            return [
                'telepon' => '(021) 12345678',
                'alamat' => 'Gerbang Kuning Gudang Bumbu, Jalan Ceuri no 51 Kampung Sindang Asih, Katapang Pamentasan, Kabupaten Bandung, Jawa Barat 40921',
                'email' => 'info@gafi.co.id',
                'bank' => 'BCA',
                'no_rekening' => '1234567890'
            ];
        }
        
        return [
            'telepon' => $admin->telepon ?: '(021) 12345678',
            'alamat' => $admin->alamat ?: 'Gerbang Kuning Gudang Bumbu, Jalan Ceuri no 51 Kampung Sindang Asih, Katapang Pamentasan, Kabupaten Bandung, Jawa Barat 40921',
            'email' => $admin->email ?: 'info@gafi.co.id',
            'bank' => $admin->bank ?: 'BCA',
            'no_rekening' => $admin->no_rekening ?: '1234567890'
        ];
    }
    
    /**
     * Get admin phone number
     */
    public static function getAdminPhone()
    {
        $identity = self::getAdminIdentity();
        return $identity['telepon'];
    }
    
    /**
     * Get admin address
     */
    public static function getAdminAddress()
    {
        $identity = self::getAdminIdentity();
        return $identity['alamat'];
    }
    
    /**
     * Get admin email
     */
    public static function getAdminEmail()
    {
        $identity = self::getAdminIdentity();
        return $identity['email'];
    }
    
    /**
     * Get admin bank info
     */
    public static function getAdminBank()
    {
        $identity = self::getAdminIdentity();
        return $identity['bank'];
    }
    
    /**
     * Get admin account number
     */
    public static function getAdminAccountNumber()
    {
        $identity = self::getAdminIdentity();
        return $identity['no_rekening'];
    }
}
