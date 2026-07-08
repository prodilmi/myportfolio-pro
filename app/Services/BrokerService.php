<?php
/**
 * Broker Service
 */

namespace App\Services;

use App\Core\Encryption;

class BrokerService {
    private $db;
    private $encryption;
    
    public function __construct($database) {
        $this->db = $database;
        $this->encryption = new Encryption();
    }
    
    /**
     * Connect a broker
     */
    public function connect($user_id, $broker_name, $account_number, $api_key, $api_secret) {
        return $this->db->insert('broker_connections', [
            'user_id' => $user_id,
            'broker_name' => $broker_name,
            'account_number' => $account_number,
            'api_key' => $this->encryption->encrypt($api_key),
            'api_secret' => $this->encryption->encrypt($api_secret),
            'status' => 'connected',
            'connected_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get broker details (with decrypted credentials)
     */
    public function getWithCredentials($broker_id) {
        $broker = $this->db->findOne('broker_connections', ['id' => $broker_id]);
        if ($broker) {
            $broker['api_key'] = $this->encryption->decrypt($broker['api_key']);
            $broker['api_secret'] = $this->encryption->decrypt($broker['api_secret']);
        }
        return $broker;
    }
    
    /**
     * Sync broker data
     */
    public function sync($broker_id) {
        $broker = $this->getWithCredentials($broker_id);
        if (!$broker) return false;
        
        // TODO: Implement actual broker API sync
        // This would connect to the broker's API and fetch latest holdings
        
        return $this->db->update('broker_connections',
            ['last_sync' => date('Y-m-d H:i:s')],
            ['id' => $broker_id]
        );
    }
    
    /**
     * Disconnect broker
     */
    public function disconnect($broker_id) {
        return $this->db->delete('broker_connections', ['id' => $broker_id]);
    }
    
    /**
     * Get user's brokers
     */
    public function getUserBrokers($user_id) {
        $brokers = $this->db->findAll('broker_connections', ['user_id' => $user_id]);
        // Remove sensitive data
        foreach ($brokers as &$broker) {
            unset($broker['api_key'], $broker['api_secret']);
        }
        return $brokers;
    }
}
