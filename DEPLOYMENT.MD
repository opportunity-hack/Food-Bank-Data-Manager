# Matthew's Crossing Data Manager (MCDM)
### Deployment Instructions
---

#### System Requirements
MCDM consists of two main components: "FBM Utility", a collection of Python scripts that interface with SoxBox's FoodBank Manager and generate reports, and "DonorTrack", a web application which provides a user-friendly interface to FBM Utility. The system requirements for these components are as follows:

##### FBM Utility
FBM Utility requires the latest Python 2.7.15rc1 (compatibility with other 2.7.x releases is likely but not tested). It requires the following Python modules:

- pandas
- numpy
- requests
- dateutil
- plotly
- mysql-connector
- openpyxl

FBM Utility also requires access to an instance of SoxBox's FoodBank Manager, to a MySQL server (shared with DonorTrack), to a Plotly account. The related "Email Sender" component requires access to a GMail account.

##### DonorTrack
DonorTrack is developed, tested, and officially deployed on a Ubuntu 18.04 LTS LAMP (Linux, Apache 2, MySQL 5.7, PHP 5.6) stack. Any equivalent configuration is likely to work, but is not tested. It requires the "gd" module for PHP.

---

#### Application Deployment
Assuming system requirements are satisfied, application deployment is fairly simple, with five tasks:

- Clone the master branch of this git repository:  
`git clone https://github.com/2018-Arizona-Opportunity-Hack/Team15-Matthews-Crossing-Data-Manager.git`
- Initialize and clone the "qrgen" submodule:  
`git submodule init`  
`git submodule update`
- Set the webroot to the DonorTrack folder (typically in `/etc/apache2/sites-available/000-default.conf`)
- Ensure Apache has full access to the webroot (e.g. `sudo chown -R www-data:www-data /path/to/mcdm`)
- Fill out the five self-explanatory private configuration files ending in ".EDITME", and remove ".EDITME" from their filenames:
  - DonorTrack/cxa/php/secret-config.php.EDITME
  - Email Sender/email_config.json.EDITME
  - FBM Utility/database_info.json.EDITME
  - FBM Utility/MCFB_Auth.json.EDITME
  - FBM Utility/plotly_auth.json.EDITME

It is highly recommended to configure the web server to use HTTPS (and auto-redirect and HTTP URLs to the HTTPS equivalent). The free service LetsEncrypt makes this process simple and largely automatic, and is recommended for deployment.
