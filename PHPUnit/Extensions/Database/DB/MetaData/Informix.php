<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    DbUnit
 * @author     Yong Soo, Ok <okyongsu@daou.co.kr>
 * @copyright  2002-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 1.0.0
 */

/**
 * Provides functionality to retrieve meta data from an IBM Informix database.
 *
 * @package    DbUnit
 * @author     Yong Soo, Ok <okyongsu@daou.co.kr>
 * @copyright  2002-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DB_MetaData_Informix extends PHPUnit_Extensions_Database_DB_MetaData
{

    /**
     * The character used to quote schema objects.
     */
    protected $schemaObjectQuoteChar = '';

    /**
     * columns
     *
     * @var array
     */
    protected $columns = array();

    /**
     * keys
     *
     * @var array
     */
    protected $keys = array();


    /**
     * The command used to perform a TRUNCATE operation.
     *
     * @var string
     */
    protected $truncateCommand = 'DELETE FROM';


    /**
     * Returns an array containing the names of all the tables in the database.
     *
     * @return array
     */
    public function getTableNames()
    {
        $tabnames = array();

        $sql = "SELECT tabname
                  FROM systables
                 WHERE tabid > 99
                   AND tabtype = 'T'
                 ORDER BY tabname ";

        $result = $this->pdo->query($query);

        while ($tabname = $result->fetchColumn(0)) {
            $tabnames[] = $tabname;
        }

        return $tabnames;
    }


    /**
     * Returns an array containing the names of all the columns in the
     * $tabname table,
     *
     * @param string $tabname
     * @return array
     */
    public function getTableColumns($tabname)
    {
        if (isset($this->columns[$tabname]) === false) {
            $this->loadColumnInfo($tabname);
        }

        return $this->columns[$tabname];
    }


    /**
     * Returns an array containing the names of all the primary key columns in
     * the $tabname table.
     *
     * @param string $tabname
     * @return array
     */
    public function getTablePrimaryKeys($tabname)
    {
        if (isset($this->keys[$tabname]) === false) {
            $this->loadColumnInfo($tabname);
        }

        return $this->keys[$tabname];
    }


    /**
     * loads colum info from Informix database
     *
     * @param string $tabname table name
     *
     * @return void
     */
    public function loadColumnInfo($tabname)
    {
        $this->columns[$tabname] = array();
        $this->keys[$tabname]    = array();

        $colSql = "SELECT a.colname, a.colno
                     FROM syscolumns a,
                          systables  b
                    WHERE a.tabid   = b.tabid
                      AND b.tabname = '$tabname'
                    ORDER BY a.colno ASC";

        $result = $this->pdo->query($colSql);

        while ($colname = $result->fetchColumn(0)) {
            $this->columns[$tabname][] = $colname;
        }

        $keySql = "SELECT d.colname
                     FROM systables      a,
                          sysconstraints b,
                          sysindexes     c ,
                          syscolumns     d
                    WHERE a.tabname    = '$tabname'
                      AND a.tabid      = b.tabid
                      AND a.tabid      = c.tabid
                      AND a.tabid      = d.tabid
                      AND b.constrtype = 'P'
                      AND b.idxname    = c.idxname
                      AND (
                             colno = part1  OR
                             colno = part2  OR
                             colno = part3  OR
                             colno = part4  OR
                             colno = part5  OR
                             colno = part6  OR
                             colno = part7  OR
                             colno = part8  OR
                             colno = part9  OR
                             colno = part10 OR
                             colno = part11 OR
                             colno = part12 OR
                             colno = part13 OR
                             colno = part14 OR
                             colno = part15 OR
                             colno = part16
                          )";

        $result = $this->pdo->query($keySql);

        while ($colname = $result->fetchColumn(0)) {
            $this->keys[$tabname][] = $colname;
        }
    }


}


/* ÆÄÀÏ³¡ */