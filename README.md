# Bumsys
An open sources project called Business Management System.


## Requirements
PHP Version: At least 7.0.  
MySQL Version: At least 5.6

## Demo
For see a demo please visit following address:
- Link: https://demo.bumsys.org/
- Username: bumsys
- Password: 12345678


## Installation
Create a MySQL database and import the following file:
`/include/db/database.sql`

Then, Edit the following file
`/include/db/db.php`

And put the correct correct database credentials. 

Now run following SQL command:

``UPDATE bms_options SET `option_value` = 'bumsys.org' WHERE `option_name` = 'rootDomain'; ``

Where bumsys.org will your domain or IP address.

Please note, the domain must be in following format:
- The domain can not be begin with transfer protocol (http://)
- The domain can not be ended with a slash (/)
- ~~https://bumsys.org~~ (incorrect)
- ~~bumsys.org/~~ (incorrect)
- **example.com** (correct)


## Contribution
If you want to contribute in this project, you are welcome!  
You can help us on this project by **Testing**, increasing the security by **Finding Security Issue**.  
Please feel free to open an issue if you found anything ugly. 

Thank you.
