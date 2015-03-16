<?php

/* 
 * Copyright (c) 2015, Kevin Kabatra 
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 
 * 1. Redistributions of source code must retain the above copyright notice, 
 *    this list of conditions and the following disclaimer. 
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 */

/* 
 * The code follows the Follow Field Naming Conventions from the 
 * AOSP (Android Open Source Project) Code Style Guidelines for Contributors :
 *     Non-public, non-static field names start with m.
 *     Static field names start with s.
 *     Other fields start with a lower case letter.
 *     Public static final fields (constants) are ALL_CAPS_WITH_UNDERSCORES
 * Hyperlink: (too long for one line)
 *     http://source.android.com/source/code-style
 *     .html#follow-field-naming-conventions
 */
  
    /**
     * Creates a new connection to a MySQL database
     * <p>
     * Example code:
     *     try {
     *         $connection = connectDatabase('databaseName', 'serverName'
     *                 , 'username', 'password');
     *     } catch (PDOException $pdoException) {
     *         handlePdoExceptions($pdoException->getMessage()); 
     *     } finally {
     *         unset($connection);
     *     }
     * <p>
     * @param type $mDatabaseName
     * @param type $mServerName
     * @param type $mUsername
     * @param type $mPassword
     * @return \PDO
     */
    function connectDatabase($mDatabaseName, $mServerName, $mUsername,
            $mPassword) {
        
        try {
            //Open a new PDO Connection
            $mConnection = new PDO("mysql:host=$mServerName;"
                    . "dbname=$mDatabaseName", $mUsername, $mPassword);
            //Set the PDO error mode to exception
            $mConnection->setAttribute(PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION);
            
            //unset() destroys the specified variables.
            unset($mDatabaseName);
            unset($mServerName);
            unset($mUsername);
            unset($mPassword);
            
            return $mConnection;
        } catch (PDOException $mPdoException) {
            handlePdoExceptions($mPdoException->getMessage());            
        }
    }
    
    /**
     * Used to get the ID of the last inserted/updated record, requires 
     * an AUTO_INCREMENT field.
     * <p>
     * Example code:
     *      $lastId = getLastInsertID($connection);
     * <p>
     * @param type $mConnection
     * @return type
     */
    function getLastInsertID($mConnection) {
        return $mConnection->lastInsertId();
    }
    
    /**
     * Used to insert records from a MySQL database table.
     * <p>
     * Example code:
     * try {        
     *     $connection = connectDatabase('databaseName', 'serverName'
     *             , 'username', 'password');
     *     //Send subjects, salt to bizTwoBusiness_subjects
     *     $tableName = "subjects";
     *     //Fields and Values to send to database
     *     $postParameter = array(
     *             'key1' => value1,
     *             'key2' => value2,
     *             'key3' => value3,
     *             'key4' => value4,
     *     );                
     *     insertIntoDatabase($connection, $tableName, $postParameter);
     * } catch (PDOException $pdoException) {
     *     handlePdoExceptions($pdoException->getMessage()); 
     * } finally {
     *     unset($connection);
     * }
     * <p>
     * @param type $mConnection
     * @param type $mTableName
     * @param type $mPostParameter
     */
    function insertIntoDatabase($mConnection, $mTableName, $mPostParameter) {
        
        try {
            //prepare sql
            $mStatement = $mConnection->prepare("INSERT INTO $mTableName (" 
                    . getFields($mPostParameter) . ") Values (" 
                    . getBindFields($mPostParameter) . ")");

            //bind parameters
            $mKey = $mValue = null;
            foreach($mPostParameter as $mKey => $mValue) {
                $mStatement->bindValue(':' . $mKey, $mValue);
            }            
            
            //row insertion
            $mStatement->execute();        
        } catch(PDOException $mPdoException) {
            handlePdoExceptions($mPdoException->getMessage());
        } finally {
            unset($mStatement);
        }                       
    }
  
    /**
     * Used to delete records from a MySQL database table.
     * <p>
     * Example code:
     *     $postParameter = array(
     *             'id' => 1,
     *     );
     * 
     *     try {
     *         $connection = connectDatabase('databaseName', 'serverName'
     *                 , 'username', 'password');
     *         deleteFromDatabase($connection, 'tableName', $postParameter);
     *     } catch (PDOException $pdoException) {
     *         handlePdoExceptions($pdoException->getMessage()); 
     *     } finally {
     *         unset($connection);
     *     }
     * <p>     
     * @param type $mConnection
     * @param type $mTableName
     * @param type $mPostParameter
     */
    function deleteFromDatabase($mConnection, $mTableName, $mPostParameter) {
        
        try {
            //prepare sql
            $mStatement = $mConnection->prepare("DELETE FROM $mTableName WHERE"
                    . " (" . getFields($mPostParameter) . " = " 
                    . getBindFields($mPostParameter) . ")");

            //bind parameters
            $mKey = $mValue = null;
            foreach($mPostParameter as $mKey => $mValue) {
                $mStatement->bindValue(':' . $mKey, $mValue);
            }            
            
            //row insertion
            $mStatement->execute();        
        } catch(PDOException $mPdoException) {
            handlePdoExceptions($mPdoException->getMessage());
        } finally {
            unset($mStatement);
        }        
    }

    /**
     * Used to select records from a MySQL database table.
     * <p>
     * @param type $mConnection
     * @param type $mTableName
     * @param type $mPostParameter
     * @return boolean
     */
    function selectFromDatabase($mConnection, $mTableName, $mPostParameter) {
        //prepare sql
        $mStatement = $mConnection->prepare("SELECT " 
                . getFields($mPostParameter) . "FROM $mTableName");
        $mResult = $mConnection->query($mStatement);

        if($mResult->num_rows > 0) {
            $mOutput = array();
            $mInnerArray = array();
            // output data of each row
            $mKey = $mValue = null;
            $mLoopIteration = 0;
            
            /*
             * While loop is responsible for creating $mOutput, multidimensional
             * array which will be returned. Foreach loop is responsible for 
             * creating $mInnerArray which will be inserted into $mOutput.
             * <p>
             * Example of $mOutput:
             *     1) $mOutput = array(
             *                array($mKey => $mRow[$mKey]),
             *                array('id' => 1),
             *        )
             * 
             */
            while($mRow = $mResult->fetch_assoc()) {
                foreach($mPostParameter as $mKey => $mValue) {
                    //$mStatement->bindValue(':' . $mKey, $mValue);
                    $mInnerArray[$mKey] = $mRow[$mKey];
                }
                $mOutput[$mLoopIteration] = $mInnerArray;
                $mLoopIteration++;
            }            
        } else {
            $mOutput = false;
        }
        
        unset($mTableName);
        unset($mStatement);
        unset($mResult);
        unset($mKey);
        unset($mValue);
        unset($mLoopIteration);
        unset($mRow);
        unset($mInnerArray);

        return $mOutput;        
    }
    
    /**
     * 
     * @param type $mPostParameter
     * @return type
     */
    function getFields($mPostParameter) {
        include_once '../database_tools/get_post_parameter_fields_values.php';
        $mFields = getPostParameterFieldsValues("field", $mPostParameter);        
        return $mFields;
    }
    
    /**
     * 
     * @param type $mPostParameter
     * @return type
     */
    function getBindFields($mPostParameter) {
        include_once '../database_tools/get_post_parameter_fields_values.php';
        $mBindFields = getPostParameterBindFields($mPostParameter);
        return $mBindFields;
    }
    
    /**
     * 
     * @param type $mPdoException
     */
    function handlePdoExceptions($mPdoException) {        
        echo $mPdoException;
        if(strpos($mPdoException, "SQLSTATE[23000]") !== false) {
            echo "duplicate entry";
            if(strpos($mPdoException, "'subject'") !== false) {
                //TODO: Provide a link to recover account
                echo "Error: that username already exists.";
            } else if(strpos($mPdoException, "'id'") !== false) {
                //TODO: Provide logging to a developer console
                //critical error, the database did not automatically auto increment                
            }            
        }
        
        if(strpos($mPdoException, "SQLSTATE[28000]") !== false) {
            //TODO: Provide logging to a developer console
            //critical error, the database did not log in using username and password
        }
        
        if(strpos($mPdoException, "SQLSTATE[42000]") !== false) {
            //TODO: Provide logging to a developer console
            //You have an error in your SQL syntax
        }
        return $mPdoException;
    }    
