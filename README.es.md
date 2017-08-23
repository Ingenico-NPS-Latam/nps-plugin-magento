# Magento Plugin

*Leer en otros lenguajes: [English](README.md), [Español](README.es.md)*

## Disponibilidad
Probado y soportado por las versiones 1.7.x.x to 1.9.x.x de Magento

## Pasos para la instalacion
1. Clonar el proyecto.
2. Copiar el contenido del plugin en el raiz de instalacion de Magento  
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

  *Con esto se agrega la Opcion NPS al menu de Magento*  

4. Configure your new NPS payment method ==> System ==> Configuration ==>  Payment Methods:  
  Completar con la informacion del Comercio:  

  ![5](https://cloud.githubusercontent.com/assets/24914148/25488627/df84ef82-2b3d-11e7-94e8-e35a4be558f2.png)  

  Enable ==> Yes   ||  No  
  New order status  ==> Processing  
  Payment Action ==> Autorize  || Authorize and Capture  
  Title  ==> NPS (Net Payment Service)  
  Secret Key  ==> Secret Key Asignado al comercio  
  Gateway URL ==> https://implementacion.nps.com.ar/ws.php?wsdl  
  Merchant ID  ==>  Merchant_id Asignado al comercio  
  Merchant's Email ==> mail@mail.com  
  Credit Card Types ==> Elegir las Tarjetas con las que se quiere operar  
  Payment from Applicable Countries ==> All Allowed Countries  || Specific Countries  
  Payment from Specific Countries ==> Elegir los paises en los que se quiere operar  

5. Configure installment settings to allow payments ==> Clicke en NPS del Menu Principal ==> Installments ==> Add Installments  

  ![6](https://cloud.githubusercontent.com/assets/24914148/25488628/df89e1fe-2b3d-11e7-8613-f1ad2486e7b5.png)  

  cc_type ==> Marca de la Tarjeta  
  qty ==> Cantidad de Cuotas  
  rate ==> Interes  
  Status ==> Enable   ||   Disable  
  Country ==> AR  
  Currency ==> ARS  

  ![7](https://cloud.githubusercontent.com/assets/24914148/25488629/dfceaa0a-2b3d-11e7-888e-ffc6130891dc.png)

## Consideraciones Finales
Si no se realizo durante la instalacion, hay que verificar / Configurar las opciones de Pais y Moneda  
  System ==> Configuration ==> General  

![8](https://cloud.githubusercontent.com/assets/24914148/25488630/dfe32c64-2b3d-11e7-9132-23dcf6b1ed2d.png)

Configurar las opciones de Pais  
  System ==> Configuration ==> Currency Setup  
Configurar las opciones de moneda dentro de la ocpion Currency Options  

![9](https://cloud.githubusercontent.com/assets/24914148/25488631/dfecbc02-2b3d-11e7-9480-6b28a992910d.png)

![10](https://cloud.githubusercontent.com/assets/24914148/25488632/e0400ab0-2b3d-11e7-8a80-74feba06f70e.png)  


# Configuración de Multiples Tiendas
*Esta sección es una transcripción de otro sitio. Articulo original  [aqui](https://www.properhost.com/support/kb/30/How-To-Setup-Magento-With-Multiple-Stores-And-Domains)*

## Agregar una nueva tienda en Magento
Los siguientes pasos son para crear una tienda actua en Magento. Digamos que queremos agregar una nueva tienda **mysecondstore.com**.

1. Nos autenticamos en el admin
2. Vamos a **Catálogo -> Administrar categorías**

 ![magento-categories](https://user-images.githubusercontent.com/6628557/29469927-c5ad5310-8420-11e7-8e1a-1afe985559f6.jpg)

3. Pulsamos en Añadir **categoría padre**

 ![magento-root-catalog](https://user-images.githubusercontent.com/6628557/29470399-ad54f370-8422-11e7-845e-121e854c8da4.jpg)

4. Introducir el nombre para la nueva categoría y asegurarte que **Is Active** esta en **True**
5. Ir a la etiqueta **Display Settings** y configurar **Is Anchor** en **True**. Esto mostrará los productos listados en subcategorias, y también habilita la funcionalidad de drill-down de productos (filtrado) por categoria.

 ![magento-category-anchor1](https://user-images.githubusercontent.com/6628557/29470516-22041d9a-8423-11e7-867d-9980798232d3.jpg)

6. Presionar **Save Category**

## Configuración de Tienda
Antes de comenzar con la configuración de nuestra tienda, hagamos una pausa y exploquemos los conceptos de sitio web (Websites), Tienda (Stores) y Vista de Tienda (Store Views) en Magento. Websites es la entidad mas alta en Magento. Si necesita sitios completamente separados que no comparten carrito, métodos de envío, etc., debe crear sitios web separados. Cada sitio web tiene al menos una Tienda, y cada tienda tiene al menos una Vista de Tienda. Tiendas Multiples pueden compartir carrito de compras, sesiones de usuario, pasarela de pago, etc., pero tienen su propia estructura de catálogo. Finalmente, una Tienda es uan colección de Vistas de Tienda. Vista de Tienda cambia la forma de presentar las páginas, normalmente se utiliza para ofrecer un sitio en diferentes diseños o idiomas.

1. Ir a **Sistema > Administrat Tienda**
2. Presionar **Crear Sitio Web** e introducir la siguiente información:
*(Saltar este paso si no desea un Sitio Web separado)*
  + **Nombre** - introducir el nombre del Sitio Web nuevo
  + **Código** - introducir el identificador único para este Sitio Web  
    *(Tome nota de este código, ya que lo necesitara más tarde!)*

   ![magento-create-website](https://user-images.githubusercontent.com/6628557/29471043-ede87e82-8424-11e7-9857-36d258e6df40.jpg)

3. Presionar **Crear Tienda** e introducir los siguientes datos:
  + **Sitio Web** - seleccione el Sitio Web que acaba de crear de la lista;
  + **Nombre** - introducir el nombre de la Tienda;
  + **Categoría Padre** - seleccione la categoría que creo en el paso anterior.

 ![magento-add-store](https://user-images.githubusercontent.com/6628557/29471257-b31249a4-8425-11e7-8a47-f9a5199080a3.jpg)

4. Presionar **Crear Vista de Tienda** y la información a continuación:
  + **Tienda** - seleccione la Tienda creada en el paso anterios
  + **Nombre** - introducir el nombre de la vista de tienda
  + **Código** - introducir el identificador único para esta vista de tienda
  *(Tome nota de este código, ya que lo necesitara más tarde!)*

  Aquí es donde agregará, por ejemplo Vista de Tiendas adicionales si planea configurar un sitio para cada idioma que admita.

  ![magento-add-storeview](https://user-images.githubusercontent.com/6628557/29471410-5092cf00-8426-11e7-8472-342b3ffcd09b.jpg)

5. Ahora, desde **Sistema > Configuración > General**
6. Asegúrese de que **Default Config** está seleccionada como **Current Configuration Scope**

  ![magento-config-scope](https://user-images.githubusercontent.com/6628557/29472815-a7cbb8fe-842b-11e7-9284-d285e52ba8ec.jpg)

7. En la solapa **Web**, configure **Auto-redirect to Base URL** en **No** y presione Guardar Configuración.

 ![magento-web-settings](https://user-images.githubusercontent.com/6628557/29473012-6e59f526-842c-11e7-8993-2362cf57b63c.jpg)

8. Ahora, cambie de las opciones **Configuration Scope** por el nuevo sitio web creado
9. En la sección **Web**, necesitamos cambiar la configuración de **Secure Base URL** y **Unsecure Base URL**. Desmarque la **Use Default [STORE VIEW]**, y reemplace la URL con su nombre de dominio correspondiente. Recuerde finalizar con  /.

 ![magento-site-urls](https://user-images.githubusercontent.com/6628557/29473092-c2e9433a-842c-11e7-9c23-b5c16cf16215.jpg)

10. Presione **Guardar Configuración**

Esto complera la configuración de una nueva Tienda. Repita estos pasos por cada Tienda adicional que necesita agregar.

## Mapeo de dominio
Hasta ahora hemos añadido dominios adicionales al servidor y hemos configurado una nueva Tienda en Magento. Ahora solo necesitamos pegar todo diciendole a Magento que Tienda cargar basado en el nombre de dominio en el que está el usuario.

1. En el servidor, abrir el **.htaccess file**, situada en el directorio raíz de la instalación de Magento, en su editor de texto favorito. También puede utilizar el administrador de archivos en su panel de control.
2. Agregar las siguientes líneas (reemplazar con el nombre de dominio actual):
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
Nota: La directiva **SetEnvIf** no está soportada por todos los servidores web (e.g. LiteSpeed). En ese caso, el código de Tienda puede ser configurado de esta forma:

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

  + MAGE_RUN_CODE - este es el código único elegido cuando creó el Sitio Web / Vista de Tienda de Magento
  + MAGE_RUN_TYPE - dependiendo de si desea cargar un sitio específico o una Vista de Tienda; Configure para Tienda o Sitio Web, respectivamente.

  Agregue uan entrada por cada dominio adicional que configuró.

3. Guardar el archivo

Ahora navegar a su nuevo sitio y verifique el correcto funcionamiento de la tienda cargada en Magento. Si no es así, asegurese que el MAGE_RUN_CODE coincide con el creado anteriormente y que los dominios resuelvan a la ruta/carpeta correcta.
