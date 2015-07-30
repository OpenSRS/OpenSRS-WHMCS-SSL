OpenSRS SSL Module
Version 2.0.1
---------------------

README 07/11/14:

OpenSRS has developed 6 add on modules for use with WHMCS v5.3.x. To install each module (except Domains Pro), FTP to your WHMCS installation, and upload the entire folder to /modules/servers/. To install Domains Pro, please follow the README instructions included with that module. 

Please note that these modules are released as-is and open-source. OpenSRS will continue to support and release updates to these modules at http://opensrs.com/site/integration/tools/whmcs.

help@opensrs.com


Requirements:
- WHMCS 5.3.x+
- PHP 5.2+
- PEAR - http://pear.php.net/
- mcrypt - http://www.php.net/manual/en/book.mcrypt.php
- getmypid() enabled
- 'TCP Out' ports 51000, 55000 and 55443 have to be open on the server for lookups and http(s) connections to OpenSRS API


####################################
Installation Instructions (v5.3.x+):
####################################

PLEASE COMPLETE THE FOLLOWING BEFORE PROCEEDING:
- You must authorize your server IP in RWI before you begin. To do this visit RWI, login, scroll to the bottom, click the link  "Add IPs for script/API access" and put your IP in the address field. Note that it may take up to 1 hr for this to propagate. 


####################################
Product Configuration:
####################################

To begin using this module, login to your WHMCS installation and select Setup->Products/Services->Products/Services. Create a new group or select Create A New Product.

Product Type: Other
Product Group: <Select One>
Product Name: <Enter Product Name>

Select Module Settings tab and choose the OpenSRS SSL module. Add your reseller username and your API key. Make sure to set the pricing term to match your reseller pricing term and any upgrade options. Finally, complete the remaining tabs and your customers will be able to order these services immediately.

**If you want to test these modules on our test server, it will require that you generate a separate API key from the test interface.


####################################
OpenSRS SSL Module:
####################################

To setup OpenSRS SSL, add a new product and configure the 'modules settings' tab:

Username: <opensrs username>
API Key: <your opensrs API key>
Default registration period: 1
Server count: 1
Certificate Type: Select the SSL you want to create. You'll need to create a separate product for each SSL.

To manage existing certificates you need to:
1. Create order from WHMCS admin panel
2. Set created product to active (do not use "Create Command").
3. When product is active you should see label "Edit Certificate" below module commands
4. Set up Order ID, Certificate Request, Approver Email and select Server Type
5. Click on save. If you see "Service Details" than everything should working fine.

**A customer must complete the order by entering in their CSR Key and confirming the certificate with the supplier before it will appear as provisioned in the RWI.


###################################
Email Templates:
####################################

We've included some default email templates with each module. We strongly advise that you edit these to match your brand. Select Setup->Email Templates and look under Product Messages to edit the available Welcome Emails. When setting up your products, select "Other" for welcome email. You can also set termination email templates from the Product page. 




####################################
CHANGES
####################################

Release 2.0.1
- Updated PHP Toolkit

Release 1.2
- Added additional server types

Release 1.1
- Fixes an issue where the domainsync.php cron would fail

Release 1.0
- spelling error: get_certyficate   =>  get_certificate

Beta 11 
- Fixes a bug where the script would produce an error when submitting a CSR
- Updated the TrustWave certificate names to match the OpenSRS names
- Added the ability to copy contact information from owner to technical and admin contact forms

Beta 10
- Fixes a bug where a user might be logged out when submitting a CSR

Beta 9
- Fixed Trustwave configuration issue

Beta 8
- Fixed an issue related to Trustwave Premium SSL Wildcard
- Added additional Comodo SSL support

Beta 7
- Fix a typo for the Trustwave certs


Beta 6
- Fixes an issue where an error would be generated after a user submits a CSR the first time


Beta 5
- Fixes an issue where the admin contact address may be incorrect for validating DV certs
- Fixes an incorrect url in email templates

Beta 4
- Bug fixes
- You can now add in existing SSL certs to WHMCS
