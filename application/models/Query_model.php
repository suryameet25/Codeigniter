<?php

Class Query_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function records($query = false) {

        $this->load->database('default');

        if (!empty($query['id'])) {
            $this->db->where($query['table'] . '.id', $query['id']);
            if (empty($query['return']))
                $query['return'] = 'row';
        }
        if (empty($query['offset']))
            $query['offset'] = 0;
        if (!empty($query['limit']))
            $this->db->limit($query['limit'], $query['offset']);
        if (!empty($query['select']))
            $this->db->select($query['select']);
        if (!empty($query['where']))
            $this->db->where($query['where']);
        if (!empty($query['where_in']))
            $this->db->where_in($query['where_in'][0], $query['where_in'][1]);
        if (!empty($query['where_not_in']))
            $this->db->where_not_in($query['where_not_in'][0], $query['where_not_in'][1]);
        if (!empty($query['like']))
            $this->db->like($query['like']);
        if (!empty($query['not_like']))
            $this->db->not_like($query['not_like']);

        if (!empty($query['or_where']))
            $this->db->or_where($query['or_where']);

        if (!empty($query['group_by']))
            $this->db->group_by($query['group_by']);

        if (!empty($query['order_by']))
            $this->db->order_by($query['order_by']);

        if (!empty($query['having']))
            $this->db->having($query['having']);

        if (!empty($query['join'])) {
            foreach ($query['join'] as $j_key => $join_field) {
                $this->db->join($j_key, $join_field, 'left');
            }
        }

        $result = $this->db->get($query['table']);

        $return = (!empty($query['return'])) ? $query['return'] : 'result';

        $response = ($return == 'result') ? $result->result() : $result->row();

        $count = $result->num_rows();
        $response = $count > 0 ? $response : false;
        return $response;
    }

    function save($save = false) {
        if (empty($save['table']))
            return false;
        $table = $save['table'];
        unset($save['table']);

        $this->load->database('default');

        if (empty($save['ignore_date']))
            $this->db->set('modified', date("Y-m-d H:i:s"));

        if (!empty($save['password'])) {
            $save['password'] = sha1($save['password'] . $this->config->item('salt'));
        }
        if ($save['id']) { //if id available then update
            unset($save['ignore_date']);
            $this->db->where('id', $save['id']);
            $this->db->update($table, $save);
            return $save['id']; //return updated id
        } else { //insert new record
            if (empty($save['ignore_date']))
                $this->db->set('created', date("Y-m-d H:i:s"));
            unset($save['ignore_date']);

            $this->db->insert($table, $save);
            return $this->db->insert_id();
        }
    }

    function update($table = false, $id = false, $field = false, $value = false, $ignore_date = false) {
        $save = array();
        $save['table'] = $table;
        $save['id'] = $id;
        if ($ignore_date)
            $save['ignore_date'] = true;

        $save[$field] = $value;
        return self::save($save);
    }

    function value($table = false, $id = false, $field = false) {
        if (!$value = self::records(array('table' => $table, 'id' => $id, 'fields' => $field, 'return' => 'row', 'limit' => 1)))
            return false;
        return $value->$field;
    }

    function row($table = false, $field = false, $value = false, $fields = false) {
        if (!$row = self::records(array('table' => $table, 'where' => array($field => $value), 'select' => $fields, 'return' => 'row', 'limit' => 1)))
            return false;
        return $row;
    }

    function result($table = false, $field = false, $value = false, $fields = false) {
        if (!$result = self::records(array('table' => $table, 'where' => array($field => $value), 'select' => $fields,)))
            return false;
        return $result;
    }

    function count($table = false, $where = false) {
        $this->load->database('default');

        if ($where)
            $this->db->where($where);
        $this->db->from($table);
        return $this->db->count_all_results();
    }

    function delete($table = false, $field = false, $value = false) {
        $this->load->database('default');
        $this->db->delete($table, array($field => $value));
        return true;
    }

}
