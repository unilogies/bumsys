# Bumsys
An open sources project called Business Management System.


## Requirements
PHP Version: At least 7.0.  
MySQL Version: At least 5.6

# Key Features
- Point of Sale (POS)  
- Multi Location Shop/ Warehouse  
- Stock Management  
- Product Variation, Batch Number and Unit
- Accounts Module  
- Various type of Ledger  
- Journal Creation and Records  
- Expenses Management  
- Loan Management  
- Customer Support  
- Call Center Module  
- Marketing Module  
- Production Module  
- Employee and Salary Management  
- Various Type of Reports  


## Demo
For see a demo please visit following address:
- Link: https://demo.bumsys.org/
- Username: bumsys
- Password: 12345678

## Our Future Plan
-  


## Installation
Create a MySQL database and import the following file:
`/include/db/database.sql`

Then, Edit the following file
`/include/db/db.php`

And put the correct database credentials. 

Now run following SQL command:

``UPDATE bms_options SET `option_value` = 'bumsys.org' WHERE `option_name` = 'rootDomain'; ``

Where bumsys.org will your domain or IP address.

Please note, the domain must be in following format:
- The domain can not be begin with transfer protocol (http://)
- The domain can not be ended with a slash (/)
- ~~https://bumsys.org~~ (incorrect)
- ~~bumsys.org/~~ (incorrect)
- **bumsys.org** (correct)


## Contribution
If you want to contribute in this project, you are welcome!  
You can help us on this project by **Testing**, increasing the security by **Finding Security Issue**.  
Please feel free to open an issue if you found anything ugly. 

## Contact
Please feel free to mail us when you found any big security issue: talk@bumsys.org  

## Thanks
Thanks to all contributor and security researcher
- @krizzsk  
- @ctflearner  
- @bruhbey  
- @leorac  
- @kmkalam24  

Thank you.