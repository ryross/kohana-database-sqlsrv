<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MS SQL Server native database result (sqlsrv 2.0 driver).
 *
 * @package    Kohana/Database
 * @category   Drivers
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Database_Sqlsrv_Result extends Database_Result {

	protected $_internal_row = 0;

	public function __construct($result, $sql, $as_object)
	{
		parent::__construct($result, $sql, $as_object);

		// Find the number of rows in the result
		$this->_total_rows = sqlsrv_num_rows($result);
	}

	public function __destruct()
	{
		if(is_resource($this->_result))
		{
			sqlsrv_free_stmt($this->_result);
		}
	}

	public function seek($offset)
	{
		if ($this->offsetExists($offset) AND sqlsrv_fetch($this->_result, $offset))
		{
			// Set the current row to the offset
			$this->_current_row = $this->_internal_row = $offset;

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function current()
	{
		if ($this->_current_row !== $this->_internal_row AND ! $this->seek($this->_current_row))
			return FALSE;

		// Increment internal row for optimization assuming rows are fetched in order
		$this->_internal_row++;

		if ($this->_as_object === TRUE)
		{
			// Return an stdClass
			return sqlsrv_fetch_object($this->_result);
		}
		elseif (is_string($this->_as_object))
		{
			// Return an object of given class name
			return sqlsrv_fetch_object($this->_result, $this->_as_object, $this->_object_params);
		}
		else
		{
			// Return an array of the row
			return sqlsrv_fetch_array($this->_result, SQLSRV_FETCH_ASSOC);
		}
	}
} // End Database_MSSQL_Result_Select