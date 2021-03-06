<?php
/**
 * Description: The DBObject class is the generic database object. It is not
 * to be used directly, but extended by additional classes, each
 * corresponding to a database table. This particular DBObject is for use
 * with a MySQL PDO driver.
 * Dependencies: A database connection script that uses the MySQL PDO
 * extension
 * Requirements: PHP 5.2 or higher
 */
class DBObject {

  protected $alias = NULL;
  protected $boundValues = array();
  protected $db;
  protected $fields = array();
  protected $groupBy = NULL;
  protected $having = NULL;
  protected $joins = NULL;    
  protected $limit = NULL;
  protected $numBoundValues = 0;
  protected $offset = NULL;
  protected $orderBy = NULL;
  protected $patterns = array();
  protected $prefix;  
  protected $selectList = NULL;
  protected $table;
  protected $tablePrefix = NULL;
  protected $unions = array();
  protected $valueStorage = array();
  protected $where = NULL;

  /**
   * @param object $db - The PDO database connection object
   * @param string $table - The name of the table
   * @param array $fields - The names of each field in the table
   * @param string $schema - The schema
   * @param $prefix - optional - A prefix for the table
   * @return The object for chaining 
   */
	function __construct(PDO $db, $table, array $fields, $schema, $prefix = NULL) {
	    $this->db = $db;
	    $this->prefix = $prefix;
	    $this->tablePrefix = ($prefix) ? $prefix.$table : $table;
	    $this->table = $table;      
	    foreach($fields as $key)
			$this->fields[$key] = NULL;
		return $this;
  	}

  /**
   * @param string $key - The table column name to retrieve 
   * @return mixed - The value of the key, if the key exists, FALSE otherwise
   */
  function __get($key)
  {
    return (array_key_exists($key, $this->valueStorage)) ?
        $this->valueStorage[$key] : FALSE;       
  }

  /**
   * @param string $key - The table column name to be assigned a value
   * @param $value - The value to assign to the key
   * @return boolean - TRUE if the key exists, FALSE otherwise
   */
  function __set($key, $value){
    if (array_key_exists($key, $this->fields))
    {
      $this->valueStorage[$key] = $value;
      return TRUE;
    }
    else
    {
      return FALSE;
    }
  }

  /**
   * Adds a value to be bound on query execution
   * @param mixed $value - The value(s) to be bound
   * @return string - The named value placeholder
   */
  protected function addBoundValue($value)
  {
    if (is_array($value))
    {
      $valueNames = array();
      foreach ($value as $val)
      {
        $this->numBoundValues += 1;
        $valueName = ':'.$this->numBoundValues;
        $this->boundValues[$valueName] = $val;
        $this->patterns[] = '#(\s'.$valueName.'[\s]?)#';
        $valueNames[] = $valueName;
      }
      return $valueNames;         
    }
    else
    {
      $this->numBoundValues += 1;
      $valueName = ':'.$this->numBoundValues;
      $this->boundValues[$valueName] = $value;
      $this->patterns[] = '#(\s'.$valueName.'[\s]?)#';
      return $valueName;
    }
  }

  /**
   * Adds space separators to each bound value
   * @return array - The spaced bound values
   */
  protected function getSpacedBoundValues()
  {
    $boundValues = ' '.implode(' | ', $this->boundValues).' ';
    return explode('|', $boundValues);
  }

  /**
   * @param string $alias - Sets the alias for the main table. Optional for
   * query build.
   * @return The object for chaining  
   */
  public function alias($alias)
  {
    $this->alias = $alias;
    return $this;
  }   

  /**
   * @var array $selectList - Used for determining which database fields
   * should be selected. Fields from joins may be included, but must include
   * either the full table name or alias as a prefix. 
   * @return The object for chaining
   */
  public function selectList(array $selectList)
  {
    foreach($selectList as $value)
    {
      $this->selectList[] = $value;
    }
    return $this;
  }

  /**
   * @param string $quotable - Escapes an input variable for use in an SQL
   * query. Returns the escaped string. Optional for query build.
   * @return The escaped input
   */
  public function quote($quotable)
  {
    return $this->db->quote($quotable);
  }

  /**
   * Joins will be added to the join array and processed in their array order
   * and use the ON syntax rather than USING. Optional for query build.
   * @param string $joinType - The type of join to be performed. Acceptable
   * values are 'left', 'right', 'inner', and 'full'
   * @param string $table - The name of the table to be joined with the
   * current table
   * @param string $column - The column(s) to be compared to the value
   * @param string $operator - The operator to be used in the comparison
   * @param string $value - The value to which $column is compared
   * @param string $tableAlias - optional - The alias of the joined table. If
   * not set, alias defaults to the table name
   * @return The object for chaining
   */
  public function addJoin(
    $joinType,
    $table,
    $column,
    $operator,
    $value,
    $tableAlias=NULL)
  {
    $joinAlias = ($tableAlias) ? $tableAlias : $table;
    $joinName = ($this->prefix) ? $this->prefix.$table : $table;
    $expr = "$column $operator $value"; 
    switch (strtolower($joinType)) {
      case "left":
        $this->joins[] = " LEFT JOIN $joinName AS $joinAlias ON $expr";
        break;
      case "right":
        $this->joins[] = " RIGHT JOIN $joinName AS $joinAlias ON $expr";
        break;                  
      case "inner":
        $this->joins[] = " INNER JOIN $joinName AS $joinAlias ON $expr";
        break;
      case "full":
        $this->joins[] = " FULL JOIN $joinName AS $joinAlias ON $expr";
        break;
      default:
        throw new Vm_Db_Exception(
          "'$joinType' is not a supported join type.");
    }
    return $this;
  }

  /**
   * Creates a where clause.  Optional for query build.
   * @param string $column - The column(s) to be compared to the value
   * @param string $operator - The operator to be used in the comparison. 
   * @param mixed $value - The value to which $column is compared - If
   * multiple values are entered as an array, they will be wrapped in
   * parentheses, else use a string
   * @param string (optional) $antecedent - The operator preceding the WHERE
   * clause. Acceptable values are 'AND' and 'OR' 
   * @param string (optional) $paren - Adds a paren to the WHERE clause -
   * 'open', 'close', 'both'
   * @param boolean $caseSensitive - optional - Whether or not the clause
   * should be case sensitive, defaults TRUE. If FALSE, will use
   * UTF8_GENERAL_CI
   * @param boolean $bind - optional - Whether or not $value should be a
   * bound parameter, defaults TRUE. Use FALSE when $value is a subquery
   * @return The object for chaining
   */
  public function where(
    $column,
    $operator,
    $value,
    $antecedent = NULL,
    $paren = NULL,
    $caseSensitive = TRUE,
    $bind = TRUE)
  {
    $value = ($bind) ? $this->addBoundValue($value) : $value;
    $value = (is_array($value)) ? '( '.implode(', ', $value).')' : $value;
    $operator =
      ($caseSensitive) ? $operator : 'COLLATE UTF8_GENERAL_CI '.$operator;
    switch ($paren){
      case 'open':
        $this->where[] = ($antecedent) ?
          " $antecedent ($column $operator $value"
          : " ($column $operator $value";
        break;
      case 'close':
        $this->where[] = ($antecedent) ?
          " $antecedent $column $operator $value)"
          : " $column $operator $value)";
        break;
      case 'both':
        $this->where[] = ($antecedent) ?
          " $antecedent ($column $operator $value)"
          : " ($column $operator $value)";
        break;                      
      default:
        $this->where[] = ($antecedent) ?
          " $antecedent $column $operator $value"
          : " $column $operator $value";             
    }
    return $this;       
  }

  /**
   * @param array $groupBy - The fields by which the result set should be
   * grouped. Optional for query build.
   * @return The object for chaining
   */
  public function groupBy(array $groupBy)
  {
    foreach($groupBy as $value)
    {
      $this->groupBy[] = $value;
    }
    return $this;
  }

  /**
   * Creates a HAVING clause.  Optional for query build. MUST be used in
   * conjunction with the GROUP BY clause
   * @param string $column - The column(s) to be compared to the value. Note:
   * The column is not a bound parameter in this clause
   * @param string $operator - The operator to be used in the comparison
   * @param string $value - The value to which $column is compared
   * @param string $antecedent - optional - The operator preceding the HAVING
   * clause. Acceptable values are 'AND' and 'OR'
   * @param string $function - optional - The SQL function to apply to the
   * column
   * @return The object for chaining 
   */
  public function having(
    $column,
    $operator,
    $value,
    $antecedent = NULL,
    $function = NULL)
  {
    $column = $this->addBoundValue($column);
    $column = ($function) ? "$function( $column)" : $column;
    $value = $this->addBoundValue($value);
    $this->having[] = ($antecedent) ?
      " $antecedent $column $operator $value" : "$column $operator $value";
    return $this;
  }

  /**
   * Orders the query.  Optional for query build
   * @param string $field - The field to sort by
   * @param string $sort (optional) - The sort type. Acceptable values are
   * ASC and DESC
   * @param boolean $caseSensitive - optional - Whether or not the ordering
   * should be case sensitive, defaults TRUE. If FALSE, will use
   * UTF8_GENERAL_CI
   * @return The object for chaining
   */
  public function orderBy($field, $sort = NULL, $caseSensitive = TRUE)
  {
    $sort = (strtolower($sort) == 'desc') ? 'DESC' : 'ASC';
    $sort = ($caseSensitive) ? $sort : 'COLLATE UTF8_GENERAL_CI '.$sort;
    $this->orderBy[] = ' '.$field." $sort";
    return $this;
  }

  /**
   * @param int $limit - The limit of the result set.  Optional for query
   * build.
   * @return The object for chaining
   */
  public function limit($limit)
  {
    if (!is_int($limit))
    {
      throw new Vm_Db_Exception('Limit must be an integer');
    }
    $this->limit = ($limit != 0) ? $limit : NULL;
    return $this;
  }

  /**
   * @param int $offset - The offset of the result set.  Optional for query
   * build.
   * @return The object for chaining
   */
  public function offset($offset)
  {
    if (!is_int($offset))
    {
      throw new Vm_Db_Exception('Offset must be an integer');
    }
    $this->offset = ($offset != 0) ? $offset : NULL;
    return $this;
  }

  /**
   * Clears all class variables by setting them to NULL, allow class instance
   * reuse
   * @param boolean $clearBound - optional - Whether or not the bound
   * variables should be cleared, defaults TRUE
   */
  public function clear($clearBound = TRUE)
  {
    $this->valueStorage = NULL;
    $this->selectList = NULL;
    $this->alias = NULL;
    $this->joins = NULL;
    $this->where = NULL;
    $this->groupBy = NULL;  
    $this->having = NULL;           
    $this->orderBy = NULL;
    $this->limit = NULL;
    $this->offset = NULL;
    if ($clearBound)
    {
      $this->boundValues = NULL;
      $this->numBoundValues = 0;
    }
  } 

  /**
   * Compiles the given data into a select query and returns a result set
   * based on the query
   * @param string (optional) $mode - 
   * If set to 'single', returns a single result set which may be accessed
   * through magic methods.
   * 
   * Example: $user->name or $user->{'name'} 
   *
   * If set to 'assoc', will return the result set as an associative array,
   * which can be accessed through a foreach loop
   *
   * Example: foreach ($user->select("assoc") as $row)
   *          {
   *            echo "ID = ".$row['userId']."\t";
   *            echo "Type = ".$row['firstName']."\t";
   *            echo "Parent = ".$row['lastName']."<br>";
   *          }
   *
   * If set to 'num', will return the result set as a numerical array
   *
   * If set to 'obj', will return an anonymous object with property names
   * that correspond to the column names returned in your result set
   *
   * If set to 'lazy', will return a combination of 'both' and 'obj',
   * creating the object variable names as they are accessed
   *
   * If set to 'subquery', will wrap the query in parantheses and return it
   * for use in a subquery without executing it.
   *  WARNING: Bound parameters are not used for subqueries 
   *
   * If set to "count", will return the number of rows
   *
   * Example: $number = $user->select("count");
   *
   * If set to "union", will add the query to the unions array. Note: you
   * must reuse the object to use union.
   * 
   * Example:
   * 
   * $user = new Db_User($db);
   * 
   * //First Query
   * $user->where('lastName', '=', 'Jones');
   * $user->select('union');
   * 
   * //Second Query
   * $user->where('lastName', '=', 'Smith');
   * $user->select('union');
   * 
   * //Get union results (orderBy and limit are optional)
   * $user->orderBy('lastName', 'ASC');
   * $user->orderBy('firstName', 'ASC');
   * $user->limit(25);
   * $users = $user->select('assoc');
   *
   * If set to "debug", prints the compiled query
   *
   * Example: $user->select->("debug"); 
   *
   * If left unset, the default return set is 'both', which returns the
   * combination of both an associative array
   * and a numerical array
   * @param string $selectType - optional - 'DISTINCT' or 'ALL'. Note:
   * $selectType is ignored for the first select query in a set of unions
   * @return mixed - The query result set
   */
  public function select($mode=NULL, $selectType=NULL)
  {
    if ($selectType)
    {
      $type = ($selectType == 'ALL') ? ' ALL' : ' DISTINCT';
    }
    else
    {
      $type = NULL;
    }

    $selectList = ($this->selectList) ?
      ' '.implode(', ', $this->selectList) : ' *';
    $alias = ($this->alias) ? ' AS '.$this->alias : ' AS '.$this->table;    
    $joins = ($this->joins) ? implode('', $this->joins) : NULL;
    $where = ($this->where) ? ' WHERE'.implode('', $this->where) : NULL;

    if (!$this->groupBy)
    {
      $groupBy = NULL;
      $having = NULL; 
    }
    else
    {
      $groupBy = ' GROUP BY '.implode(', ', $this->groupBy);
      $having = ($this->having) ?
        ' HAVING '.implode('', $this->having) : NULL;
    }

    $orderBy = ($this->orderBy) ?
      ' ORDER BY '.implode(', ', $this->orderBy) : NULL;

    if ($this->limit)
    {
      $limit = ($this->offset) ?
        ' LIMIT '.$this->offset.', '.$this->limit
        : ' LIMIT '.$this->limit;
    }
    else
    {
      $limit = NULL;
    }

    if ((sizeof($this->unions) > 0) && ($mode != 'union'))
    {
      $query = implode(' ', $this->unions)."$orderBy$limit";
    }
    else
    {
      $query = "SELECT$type$selectList FROM "
        .$this->tablePrefix
        ."$alias$joins$where$groupBy$having$orderBy$limit";
    }
    $result = $this->db->prepare($query);

    if ((is_array($this->boundValues))
      &&(!in_array($mode, array('subquery', 'union'))))
    {
      foreach ($this->boundValues as $name => $value)
      {
        $result->bindValue($name, $value);
      }
    }

    if (strtolower($mode) == "debug")
    {
      return preg_replace(
        $this->patterns, $this->getSpacedBoundValues(),
        $result->queryString, 1);
    }
    else if(strtolower($mode) == "subquery")
    {
      $this->clear(FALSE);
      return '('.$result->queryString.')';
    }
    else if (strtolower($mode) == "union")
    {
      $this->clear(FALSE);
      $this->unions[] = (sizeof($this->unions) >= 1) ?
        "UNION $selectType (".$result->queryString.')'
        : '('.$result->queryString.')';         
    }
    else
    {
      $result->execute();
      switch (strtolower($mode)) {
        case "assoc":
          return $result->fetchAll(PDO::FETCH_ASSOC);
        case "count":
          return count($result->fetchAll());
        case "num":
          return $result->fetchAll(PDO::FETCH_NUM);
        case "lazy":
          return $result->fetch(PDO::FETCH_LAZY);
        case "obj":
          return $result->fetchAll(PDO::FETCH_OBJ);
        case "single":
          $rows = $result->fetch(PDO::FETCH_ASSOC);
          if (is_array($rows))
          {
            foreach(array_keys($rows) as $key)
            {
              $this->valueStorage[$key] = $rows[$key];
            }
          }
          return $rows;                   
        default:
          return $result->fetchAll(PDO::FETCH_BOTH);  
      }
    }
  } 

  /**
   * The insert function inserts records into the database. Magic methods are
   * used to insert values into each field. Fields that are not assigned a
   * value will not be included in the compiled query.
   * Example usage: 
   *    $users = new Db_Users($db);
   *    $users->username = 'jDoe';
   *    $users->firstName = 'John';
   *    $users->{'lastName'} = 'Doe'; //An alternate syntax
   *    $users->{'age'} = 37;
   *    $users->insert();
   * @param string $mode - optional - 
   *  If set to 'debug', returns the compiled SQL query 
   * @return mixed - The last insert id if the query is successful, the
   * compiled query if mode is set to debug, 
   *  FALSE otherwise.
   */
  public function insert($mode=NULL)
  {
    $valueTypes = array('array'=>FALSE, 'single'=>FALSE);
    $arrayLength = 0;
    $fieldNames = array();
    $values = array();
    $params = array();

    foreach ($this->valueStorage as $name => $value)
    {
      $fieldNames[] = $name;
      if (is_array($value))
      {
        if (!$valueTypes['array'])
        {
          $valueTypes['array'] = TRUE;
        }
        if ($valueTypes['single'])
        {
          throw new Vm_Db_Exception(
          'Insert values must be either arrays '
            .'of the same length or a single value');
        }   
        $i = 0;
        foreach ($value as $inputValue)
        {
          $params[$i][] = '?';
          $values[$i][] = $inputValue;
          $i++;
        }
      }
      else
      {
        if (!$valueTypes['single']){
          $valueTypes['single'] = TRUE;
        }
        if ($valueTypes['array']) {
          throw new Vm_Db_Exception(
            'Insert values must be either arrays of the same length or a '
              .'single value');
        }
        $params[0][] = '?';
        $values[0][] = $value;
      }
    }
    $numInserts = 0;
    foreach ($params as $count=>$value)
    {
      $params[$count] = '('.implode(',', $value).')';
      $numInserts += 1;
    }
    $params = implode(',', $params);

    $numValues = sizeof($values[0]);
    $boundValues = array();
    foreach ($values as $value)
    {
      if (sizeof($value) != $numValues)
      {
        throw new Vm_Db_Exception(
          'Insert values must be either arrays of the same length or a'
            .'single value');
      }
      foreach ($value as $boundValue)
      {
        $boundValues[] = $boundValue;
      }
    }

    $query = 
      'INSERT INTO '
        .$this->tablePrefix
        .' ('
        .implode(',', $fieldNames)
        .') VALUES '
        .$params;

    $result = $this->db->prepare($query);

    if (strtolower($mode) == 'debug')
    {
      $patterns = array();
      foreach ($boundValues as $value)
      {
        $patterns[] = '#\?#';
      }
      return preg_replace($patterns, $boundValues, $result->queryString, 1);
    }
    else
    {
      $i = 0;
      foreach ($boundValues as $value)
      {
        $boundValues[$i] = $boundValues[$i];
        $result->bindValue(($i+1), $boundValues[$i]);
        $i++;
      }
      $result->execute();
      return $this->db->lastInsertId() + $numInserts - 1;
    }
  } 

  /**
   * Updates a database field with values obtained from magic methods
   * representing the field names. 
   * Notes: Multiple table updates are currently not supported, nor are
   * ordering or limiting result sets due to DBMS syntax inconsistencies
   * @param string $mode - optional - If set to 'debug', returns the compiled
   * SQL query
   * @return int - The number of affected rows 
   */
  public function update($mode = NULL)
  {
    $fields = array();
    $boundValues = array();

    foreach (array_keys($this->valueStorage) as $field)
    {
      $fields[]= "$field=";
    }
    $parameters = implode("?, ", $fields).'?';

    $i = 1;
    foreach (array_keys($this->valueStorage) as $field)
    {
      $boundValues[$i]= $this->valueStorage[$field];
      $i++;
    }

    $where = ($this->where) ? ' WHERE'.implode('', $this->where) : NULL;
    $where = preg_replace('#(\s:[\w-]+[\s]?)#', ' ? ', $where);
    $boundValues =
      array_merge($boundValues, array_values($this->boundValues));
    $boundValues = $boundValues;

    $query = "UPDATE ".$this->tablePrefix." SET $parameters$where";
    $result = $this->db->prepare($query);

    if (strtolower($mode) == 'debug')
    {
      $patterns = array();
      foreach ($boundValues as $value)
      {
        $patterns[] = '#\?#';
      }
      return preg_replace($patterns, $boundValues, $result->queryString, 1);
    }
    else
    {
      $i = 0;
      foreach ($boundValues as $value)
      {
        $boundValues[$i] = $boundValues[$i];
        $result->bindValue($i+1, $boundValues[$i]);
        $i++;
      }
      $result->execute();
      return $result->rowCount();
    }
  } 

  /**
   * The delete function deletes all rows that meet the conditions specified
   * in the where clause and returns the number of affected rows
   * @param string $mode - optional - Acceptable value is 'debug', which
   * prints the compiled query
   */
  public function delete($mode = NULL)
  {
    $where = ($this->where) ? ' WHERE'.implode('', $this->where) : NULL;
    $query = 'DELETE FROM '.$this->tablePrefix.$where;
    $result = $this->db->prepare($query);

    if (strtolower($mode) == 'debug') 
    {
      return preg_replace(
        $this->patterns,
        $this->getSpacedBoundValues(),
        $result->queryString,
        1);
    }
    else
    {
      foreach ($this->boundValues as $name=>$value)
      {
        $result->bindValue($name, $value);
      }
      $result->execute();
      return $result->rowCount();
    }
  }

  /**
   *  Description: Deletes all rows in the table, returns the number of
   * affected rows.
   * @return int - The number of affected rows
   */
  public function deleteAll()
  {
    $result = $this->db->prepare('DELETE FROM '.$this->tablePrefix);
    $result->execute();
    return $result->rowCount();
  } 
}