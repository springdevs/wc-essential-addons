<?php

namespace SpringDevs\WcEssentialAddons;

/**
 * The Database class
 */
class Database
{
    private $table;
    private $selector_column;
    private $selector_value;
    private $data;
    private $format;
    private $args;

    public function __construct($table, $selector_column = false, $selector_value = false, $data = [], $format = null, $args = [])
    {
        $this->table = $table;
        $this->selector_column = $selector_column;
        $this->selector_value = $selector_value;
        $this->data = $data;
        $this->format = $format;
        $this->args = $args;
    }

    public function create()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table;

        $wpdb->insert(
            $table_name, //table
            $this->data, //data
            $this->format //data format			
        );
    }

    public function get()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table;
        $query = "SELECT * from $table_name";

        if ($this->selector_column) {
            $query .= " WHERE {$this->selector_column}={$this->selector_value}";
        }

        if (isset($this->args['order'])) {
            $query .= " ORDER BY {$this->args['order']}";
        }

        if (isset($this->args['limit'])) {
            $query .= " LIMIT {$this->args['limit']}";
        }

        return $wpdb->get_results($query);
    }

    public function count()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table;
        $query = "SELECT count(id) AS count from $table_name";

        if ($this->selector_column) {
            $query .= " WHERE {$this->selector_column}={$this->selector_value}";
        }

        return $wpdb->get_results($query)[0]->count;
    }
}
