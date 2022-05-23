<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthenticationModel extends CI_Model
{

    public function user_exists($phoneNumber) 
    {
        $this->db->where('phone_number', $phoneNumber);
        $query = $this->db->get('users');
        return $query->row();
    }

    public function create_user($data) 
    {
        return $this->db->insert('users', $data);
    }

    public function update_code($data) 
	{
		$this->db->where(['phone_number' => $data->phone_number]);
		$query 	= $this->db->get('user_info');
		$result = $query->num_rows();
		if($result > 0) return $this->db->update('user_info', $data);
		else return $this->db->insert('user_info', $data);
	}




    # create uniqueId without initial
    private function createUniqueId() 
	{
		return rand(10,90).strtoupper(uniqid()).rand(111,999);
	}

    # create uniqueId with initial
    private function createUniqueIdWithInitial($initial) 
	{
		return $initial.strtoupper(uniqid()).rand(111,999);
	}

}