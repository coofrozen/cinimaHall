<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

	var $table = 'user_login';
	var $column_order = array('username','type','status','name','oracle_id','email','phoneNo','date_created','date_updated',null); //set column field database for datatable orderable
	var $column_search = array('name','oracle_id','phoneNo'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('id' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select();
		$this->db->from('user_login l');


		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}
	public function get_profile_pic(){
		$this->db->where('id',1);
		$this->db->select('photo');
		$query = $this->db->get($this->table);
		return $query->result();
	}
	
	public function get_pass()
	{
		$this->db->select('password');
	    $this->db->from($this->table);
		$this->db->where('id',$this->session->userdata('id'));
		$query=$this->db->get()->row();
		return $query->password;
	}

	public function check_id_availablity($user_id){

		$this->db->from($this->table);
		$this->db->where('oracle_id', $user_id);
	    $query = $this->db->get();
	    return $query->result();

	}

	public function check_id_availablity_update($user_id,$row_id){

		$this->db->from($this->table);
		$this->db->where('oracle_id', $user_id);
		$this->db->where('id!=', $row_id);
	    $query = $this->db->get();
	    return $query->result();

	}

	public function get_user_email($id){

		$this->db->select('email');
		$this->db->from($this->table);
		$this->db->where('id', $id);
	    return $this->db->get()->row('email');

	}

	public function get_reciver_name($id){

		$this->db->select('name');
		$this->db->from($this->table);
		$this->db->where('id', $id);
	    return $this->db->get()->row('name');

	}



}
