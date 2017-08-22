# Magento Plugin

*Read this in other languages: [English](README.md), [Español](README.es.md)

## Availability
Supported & Tested in Magento versions 1.7.x.x to 1.9.x.x

## Installation steps
1. Clone the project.
2. Copy the plugin content into the home directory of Magento

  ![1](https://cloud.githubusercontent.com/assets/24914148/25488577/c34daeda-2b3d-11e7-8c21-ba08d45ba890.png)

3. Erase all cache  
  system ==> Cache Management  

  ![2_cache_mangement](https://cloud.githubusercontent.com/assets/24914148/25488624/df814fb2-2b3d-11e7-86be-8e644490fe51.png)

  ![3](https://cloud.githubusercontent.com/assets/24914148/25488626/df84dfce-2b3d-11e7-8779-7ee35da1d904.png)

  ==> Flush Magento Cache  
  ==> Flush Cache Storage    
  ==> Flush Catalog and Images Cache  

  ![4](https://cloud.githubusercontent.com/assets/24914148/25488625/df848b1e-2b3d-11e7-85c0-86e7b25f780f.png)

  ==> Flush Javascript/css Cache  

  *By doing this, NPS option is added to Magento menu

4. Configure your new NPS payment method ==> System ==> Configuration ==>  Payment Methods:  
  Complete with Merchant data:  

  ![5](https://cloud.githubusercontent.com/assets/24914148/25488627/df84ef82-2b3d-11e7-94e8-e35a4be558f2.png)  

  Enable ==> Yes   ||  No  
  New order status  ==> Processing  
  Payment Action ==> Autorize  || Authorize and Capture  
  Title  ==> NPS (Net Payment Service)  
  Secret Key  ==> Secret Key Assigned to Merchant
  Gateway URL ==> https://implementacion.nps.com.ar/ws.php?wsdl  
  Merchant ID  ==>  Merchant_id Assigned to Merchant
  Merchant's Email ==> mail@mail.com  
  Credit Card Types ==> Choose cards brand to use
  Payment from Applicable Countries ==> All Allowed Countries  || Specific Countries  
  Payment from Specific Countries ==> Choose countries to use

5. Configure installment settings to allow payments ==> Click in NPS from Main Menu ==> Installments ==> Add Installments  

  ![6](https://cloud.githubusercontent.com/assets/24914148/25488628/df89e1fe-2b3d-11e7-8613-f1ad2486e7b5.png)  

  cc_type ==> Card Brand   
  qty ==> Installment quantities  
  rate ==> Interst  
  Status ==> Enable   ||   Disable  
  Country ==> AR  
  Currency ==> ARS  

  ![7](https://cloud.githubusercontent.com/assets/24914148/25488629/dfceaa0a-2b3d-11e7-888e-ffc6130891dc.png)

## Last but not least
If you did not do it during the installation, Verify (or Configure) Currency and Country  
System ==> Configuration ==> General  
![8](https://cloud.githubusercontent.com/assets/24914148/25488630/dfe32c64-2b3d-11e7-9132-23dcf6b1ed2d.png)

Configure Country options   
System ==> Configuration ==> Currency Setup  

Configure currency options    
![9](https://cloud.githubusercontent.com/assets/24914148/25488631/dfecbc02-2b3d-11e7-9480-6b28a992910d.png)
![10](https://cloud.githubusercontent.com/assets/24914148/25488632/e0400ab0-2b3d-11e7-8a80-74feba06f70e.png)  

# Multiple store setup
*This section is a transcription from other site. Source article [here](https://www.properhost.com/support/kb/30/How-To-Setup-Magento-With-Multiple-Stores-And-Domains)*

## Add new Magento store
The next step is to create the actual stores in Magento. Lets say we want to add a new store **mysecondstore.com**.

1. Log into your Magento Admin Panel
2. Click on **Catalog > Manage Categories**

 ![magento-categories](https://user-images.githubusercontent.com/6628557/29469927-c5ad5310-8420-11e7-8e1a-1afe985559f6.jpg)

3. Click on **Add Root Category**

 ![magento-root-catalog](https://user-images.githubusercontent.com/6628557/29470399-ad54f370-8422-11e7-845e-121e854c8da4.jpg)

4. Enter a name for the new category and make sure **Is Active** is set to **True**
5. Click on the **Display Settings** tab and set **Is Anchor** to **True**. This will show products listed in sub categories, and also enable product drill-down functionality (filtering) for the category.

 ![magento-category-anchor1](https://user-images.githubusercontent.com/6628557/29470516-22041d9a-8423-11e7-867d-9980798232d3.jpg)

6. Click **Save Category**

## Store configuration
Before we start configuring our stores, lets pause for a minute and explain the concepts of Websites, Stores, and Store Views in Magento. Websites are the top-most entity in Magento. If you want completely separate sites that do not share cart, shipping methods, etc., you should create separate Websites. Each Website has at least one Store, and each Store has at least one Store View. Multiple Stores can share cart, user sessions, payment gateways, etc., but have their own catalog structure. Finally, a Store is a collection of Store Views. Store Views change the way pages are presented, normally used to offer a site in different layouts or languages. These concepts can be a bit confusing at first and I recommend this webinar which explains the Magento multi-store retailing in detail.

1. Go to **System > Manage Stores**
2. Click **Create Website** and enter the following information:
*(Skip this step if you don't want separate **Websites**)*
  + **Name** - enter a name for the new website
  + **Code** - enter a unique identifier for this website  
    *(Make a note of this code as you will need it later!)*

   ![magento-create-website](https://user-images.githubusercontent.com/6628557/29471043-ede87e82-8424-11e7-9857-36d258e6df40.jpg)

3. Click **Create Store** and enter the following:
  + **Website** - select the website you just created from the dropdown;
  + **Name** - enter a name for the store;
  + **Root Category** - select the category you created in the previous step.

 ![magento-add-store](https://user-images.githubusercontent.com/6628557/29471257-b31249a4-8425-11e7-8a47-f9a5199080a3.jpg)

4. Click **Create Store View** and the information as follows
  + **Store** - select the store you created in the previous step
  + **Name** - enter a name for the store view
  + **Code** - enter a unique identifier for this store view
  *(Make a note of this code as you will need it later!)*

  This is where you will add additional store views if you plan on setting up a site for each language you support for example.
  ![magento-add-storeview](https://user-images.githubusercontent.com/6628557/29471410-5092cf00-8426-11e7-8472-342b3ffcd09b.jpg)

5. Now, go to **System > Configuration > General**
6. Make sure **Default Config** is selected as the **Current Configuration Scope**

 ![magento-config-scope](https://user-images.githubusercontent.com/6628557/29472815-a7cbb8fe-842b-11e7-9284-d285e52ba8ec.jpg)

7. On the **Web** tab, set **Auto-redirect to Base URL** to **No** and click Save Config

 ![magento-web-settings](https://user-images.githubusercontent.com/6628557/29473012-6e59f526-842c-11e7-8993-2362cf57b63c.jpg)

8. Now, change the **Configuration Scope** dropdown to you newly created website
9. Under the **Web** section, we now need to change the **Secure Base URL** and **Unsecure Base URL** settings. Uncheck the **Use Default [STORE VIEW]**, and replace the URL's with your corresponding domain name. Remember to include the trailing /.

 ![magento-site-urls](https://user-images.githubusercontent.com/6628557/29473092-c2e9433a-842c-11e7-9c23-b5c16cf16215.jpg)

10. Click **Save Configuration**

This completes the configuration of the new store. Repeat these steps for each additional store you want to add.

## Domain mapping
So far we have added the additional domains to the server and configured the new store in Magento. Now we just need to glue it together by telling Magento which store to load based on the domain name the user is on.

1. On the server, open the **.htaccess file**, located in the root directory of your Magento installation, in your favorite text editor. You can also use the File Manager in your control panel.
2. Add the following lines (replace with actual domain names):
  ```
  SetEnvIf Host www\.domain1\.com MAGE_RUN_CODE=domain1_com
  SetEnvIf Host www\.domain1\.com MAGE_RUN_TYPE=website
  SetEnvIf Host ^domain1\.com MAGE_RUN_CODE=domain1_com
  SetEnvIf Host ^domain1\.com MAGE_RUN_TYPE=website

  SetEnvIf Host www\.domain2\.com MAGE_RUN_CODE=domain2_com
  SetEnvIf Host www\.domain2\.com MAGE_RUN_TYPE=website
  SetEnvIf Host ^domain2\.com MAGE_RUN_CODE=domain2_com
  SetEnvIf Host ^domain2\.com MAGE_RUN_TYPE=website
```
Note: the **SetEnvIf** directive is not supported by all web servers (e.g. LiteSpeed). In that case, the store code can be set in this way:

  ```
  RewriteCond %{HTTP_HOST} www\.domain1\.com [NC]
  RewriteRule .* - [E=MAGE_RUN_CODE:domain1_com]
  RewriteCond %{HTTP_HOST} www\.domain1\.com [NC]
  RewriteRule .* - [E=MAGE_RUN_TYPE:website]

  RewriteCond %{HTTP_HOST} www\.domain2\.com [NC]
  RewriteRule .* - [E=MAGE_RUN_CODE:domain2_com]
  RewriteCond %{HTTP_HOST} www\.domain2\.com [NC]
  RewriteRule .* - [E=MAGE_RUN_TYPE:website]
  ```

  + MAGE_RUN_CODE - this is the unique code chosen when you created the Magento Website / Store View
  + MAGE_RUN_TYPE - depending on whether you want to load a specific Website or Store View; set it to website or store, respectively.

  Add an entry for each additional domain you have set up.

3. Save the file

Now navigate to you new site and verify that the correct Magento store is loaded. If not, make sure that the the MAGE_RUN_CODE match the one created earlier and that the domain resolves to the correct folder/path.
