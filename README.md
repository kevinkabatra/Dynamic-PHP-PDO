# Dynamic-PHP-PDO
Boilerplate for dynamic use of PHP Data Objects (PDO) through the use of arrays.

I created this code for projects that require multiple connections to different MySQL databases. Of course you can use non-MySQL databases, you will just have to change the code accordingly. Using the code you are able to eliminate the need to create seperate functions for connecting to, inserting into, deleting from, and selecting from each database.

Almost all of the functions within the boilerplate are phpDoc'd / JavaDoc'd, and include full example code on how to call those specific functions.

I have included example code below for demonstration on how to use the connectDatabase() and insertIntoDatabase() from php_data_objects.php. You should never have a need to make a call to the other files included within the boilerplate, as php_data_objects.php will make all of the calls for you.

Future plans: adding support for other MySQL functions, completing JavaDoc for all functions, adding support for other database systems.

Warm Regards,

Kevin Kabatra
    
     
    include 'php_data_objects.php';

    try {        
      $mConnection1 = connectDatabase('databaseName_subjects'
          , $serverName, $databaseUsername, $databasePassword);
      
      //Send subjects, salt to databaseName_subjects.subjects
      $mTableName = "subjects";
      
      //Fields and Values to send to database
      $mPostParameter = array(
            'subject' => $subject,
            'salt' => $salt,
            'firstName' => $firstName,
            'lastName' => $lastName,
      );
      
      insertIntoDatabase($mConnection1, $mTableName, $mPostParameter);
      
      $mConnection2= connectDatabase('databaseName_passwords'
          , $serverName, $databaseUsername, $databasePassword);
      
      //password to databaseName_passwords.passwords
      $mTableName = "passwords";
      
      //Fields and Values to send to database
      $mPostParameter = array(
            'password' => $password,
      );
      
      insertIntoDatabase($mConnection2, $mTableName, $mPostParameter);      
      
    } catch(PDOException $mPdoException) {
      echo $mPdoException;
    } finally {
      //unset() destroys the specified variables.
      unset($subject);
      unset($salt);
      unset($firstName);
      unset($lastName):
      unset($password);
      unset($mConnection1);
      unset($mConnection2);
      unset($mTableName);
      unset($mPostParameter);
      unset($mPdoException);
    }
