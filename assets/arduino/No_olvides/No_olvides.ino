/* Librerias Necesarias */

#include <Adafruit_Fingerprint.h>  // Lector de huella
#include <HTTPClient.h>            // Conexion con el servidor
#include <WiFi.h>                  // Conexion a internet
#include <WebServer.h>             // Conexion al servidor

/* Pantalla */
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
int totalColumns = 20;
int totalRows = 4;

/* Objeto de la clase lcd */
LiquidCrystal_I2C lcd(0x27, totalColumns, totalRows);

/* Credenciales para el WIFI */
#define WIFI_SSID "No-Wifi"                 // nombre de la red 2.4 ghz
#define WIFI_PASSWORD "INT3RN3T_CUL3R0123"  // contraseña de la red

int LED = 2;  // Led para encender y apagar

String url = "http://192.168.1.76/modular/arduino_resp.php";  //  Direccion del servidor local
/*  La IP cambia cada cierto tiempo segun el DHPC*/
/*  Variables para cambiar el status y recibir la informacion del servidor */
String finger_err = "-";
String transaccion_hecha = "-";
String ID = "-";
String error = "-";
String aux = "-";

/*  Creamos objeto para la libreria del sensor de huella */
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&Serial2);
uint8_t id;

void setup() {
  //Inicializamos la cosola y el lector de huella
  Serial.begin(57600);
  finger.begin(57600);
  lcd.init();
  lcd.backlight();
  // Modo del LED
  pinMode(LED, OUTPUT);
  // Inicializamos conexion a wifi con las credencailes
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  Serial.print("conectando..");
  lcd.setCursor(0, 1);
  lcd.print("   Conectando... ");

  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  // Logro conectarse
  Serial.println();
  Serial.print("connected: ");
  Serial.println(WiFi.localIP());  // Mostramos IP
  lcd.setCursor(0, 1);
  lcd.print("     Conectado!! ");
  lcd.setCursor(4, 2);
  lcd.print(WiFi.localIP());
  /* Encontro el lector de huella */
  if (finger.verifyPassword()) {
    Serial.println("Found fingerprint sensor!");
    finger_err = "Todo_bien";  // cambiamos status
  } else {
    Serial.println("Did not find fingerprint sensor :(");
    lcd.clear();
    String scrollingMessage = "Error, con el Lector de huella";

    while (1) {
      scrollMessage(1, scrollingMessage, 450, 20);
      delay(5);
    }
  }
}
/* Ciclo */
/* Ciclo */
void loop() {
  // Mientras este conectado a Wifi
  while (WiFi.status() == WL_CONNECTED) {

    HTTPClient http;  // Inicializamos cleinte HTTP

    /* LO que se va a enviar es:
    finger_err        --> Si el lector de huella si esta funcionando 
    transaccion_hecha --> que transaccion se hizo
    ID                --> ID que se manipulo  
    error             -->  confirmar transacción
*/

    String postData = "finger_err=" + finger_err + "&transaccion_hecha=" + transaccion_hecha + "&id=" + ID + "&error=" + error;

    http.begin(url);                                                      // Mandamos peticion a servidor con la URL ya establecida arriba
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");  // Necesario para enviar los datos por POST
                                                                          // Mostramos lo que envia
    Serial.println("\n ENVIADO   ");
    Serial.println(postData);
    Serial.println("\n");

    int httpCode = http.POST(postData);  // El codigo que envia el server
    delay(1000);
    /* Limpiamos variables para que no guarden basura */
    error = "-";
    transaccion_hecha = "-";
    ID = "-";
    /* LO que se va a recibir es el modo del sensor y el ID del huella a registrar o borrar, si no es nada de eso mandamos un - */
    String respuesta = http.getString();
    Serial.print("Codigo de respuesta: ");
    Serial.println(httpCode);  // El servidor respondio
    Serial.print("El server respondio: ");
    Serial.println(respuesta);
    String resp_aux = respuesta;

    if (resp_aux == "") {
      lcd.clear();
      lcd.setCursor(0, 1);
      lcd.print("Error al conectarse");
      lcd.setCursor(0, 2);
      lcd.print("   Con el Servidor");
    } else {
      lcd.clear();
      lcd.setCursor(0, 1);
      lcd.print(" Coloca tu dedo en");
      lcd.setCursor(0, 2);
      lcd.print("el Lector de Huella");
    }

    // Lo que rebibe es REGISTER/-, debemos descomponer el string para establecer los datos
    int index = respuesta.indexOf('/');  // contar espacios hasta el /
    int length = respuesta.length();
    String accion_sensor = respuesta.substring(2, index);  // EXTRAERMOS LA ACCION A REALIZAR
    ID = respuesta.substring(index + 1, length);           // Extraemos el ID o el -

    /* Si esta en modo register quiere decir que esta esperando que llegue alguien */
    if (ID == "-" && accion_sensor == "REGISTER") {

      Serial.print("ESTAMOS EN ESPERAR PARA REGISTRAR ENTRADA O SALIDA \n");

      getFingerprintID();    // Llamamos al metodo de esperar huella
      ID = finger.fingerID;  // Si detecta una huella la guardamos en ID

      if (aux != "-") {  // Mientras no encuentre nuinguna huella
        Serial.print("\n DATOS NO VALIDOS PARA ASISTENCIA \n");
        ID = "-";
      } else {                          // Ha detectado una huella registrada
        transaccion_hecha = "register"; /* Actualizamos variables */
        error = "correcto";
        /* Encedemos el LED para que el usuario se de cuenta que ya puede retirar el dedo */
        encender_led();
      }
    }

    /* Si hay avisos para la persona que registro ENTRADA/SALIDA */

    // EJEMPLO DE LO QUE RECIVE
    //AVISO/2/Nomina#Hola#2023-03-13#2023-03-16#Urgente$Aaaaa#holissss#2023-03-14#2023-03-16#Urgente$
    if (accion_sensor == "AVISO") {
      // aviso_resp elimina AVISO/ de la respuesta sel server
      String aviso_resp = respuesta.substring(8, respuesta.length());
      int nu_avisos = aviso_resp.indexOf('/');            // contar espacios hasta el /
      String ciclo = aviso_resp.substring(0, nu_avisos);  // Extraemos el numero de avisos
      // Eliminamos el numero de avisos en el string
      String avisos_aux = aviso_resp.substring(nu_avisos + 1, aviso_resp.length());
      aviso_resp = avisos_aux;
      //int ciclo = aviso_resp.indexOf('/');  // contar espacios hasta el /

      // contar espacios hasta el /
      // nu_avisos --> Guarda el numero de avisos que vienen en el string
      // num_avisos guarda el numero de avisos que mando el server
      //  aviso_resp = aviso_resp.substring(nu_avisos, aviso_resp.length());       //Volvemos a recortar la cadena
      // aviso guarda el aviso o avisos que manda el server sin el AVISO/2/
      Serial.print("\n Nu. avisos \n");
      Serial.print(ciclo);
      Serial.print("\n Estos son los Avisos \n");
      Serial.print(aviso_resp);
      int value;
      value = ciclo.toInt();
      separar_aviso(aviso_resp, value);
    }
    /* 
    Si esta en modo DELETE quiere decir que se va a borrar una huella
    AÚN NO ESTA EMPLEMENTADO
*/
    if (accion_sensor == "DELETE") {
      id = readnumber();
      Serial.print("ESTAMOS EN BORRAR \n");
      Serial.print(id);

    }

    /* Si esta en modo ENROLL quiere decir que se va a agregar una nueva huella   */

    else if (accion_sensor == "ENROLL") {
      id = readnumber();  // ID para guardar la huella

      Serial.print("ESTAMOS EN ENROLAR \n");
      Serial.print(id);

      while (transaccion_hecha != "add") { /* Hasta que la huella se agregue correctamente */
        getFingerprintEnroll();            // Llamamos a metodo
      }

      ID = String(id); /* Converimos id a string para enviarlo al server y posteriormnete  a la bd */
      error = "correcto";
      lcd.clear();
      lcd.setCursor(0, 1);
      lcd.print(" Correcto!! Huella");
      lcd.setCursor(0, 2);
      lcd.print("guardada con exito");
      encender_led();  // Encendemos el LED para que el usuario se de cuenta que se registro su huella
      delay(500);
    }

    http.end();
    delay(5000); /* Repetimos cada 5 segundos */
  }              // While
}

/*  
  Metodo para convertir el String del ID en el tipo de dato unit8_t
*/
uint8_t readnumber(void) {
  uint8_t num = 0;
  num = ID.toInt();
  return num;
}

/* 
  Método ENROLL 
  Agrega una nueva huella, 
  El método lo facilita la libreria <Adafruit_Fingerprint.h>
*/
uint8_t getFingerprintEnroll() {
  int p = -1;
  Serial.print("Waiting for valid finger to enroll as #");

  lcd.clear();
  lcd.setCursor(0, 1);
  lcd.print("Registrar una ");
  lcd.setCursor(0, 2);
  lcd.print("Nueva huella ");
  Serial.println(id);
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
      case FINGERPRINT_OK:
        Serial.println("Image taken");
        break;
      case FINGERPRINT_NOFINGER:
        Serial.println(".");
        break;
      case FINGERPRINT_PACKETRECIEVEERR:
        Serial.println("Communication error");
        break;
      case FINGERPRINT_IMAGEFAIL:
        Serial.println("Imaging error");
        break;
      default:
        Serial.println("Unknown error");
        break;
    }
  }
  // OK success!
  p = finger.image2Tz(1);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");

      lcd.setCursor(0, 1);
      lcd.print("Correcto!!");
      lcd.setCursor(0, 2);
      lcd.print(" Huella encontrada");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      lcd.setCursor(0, 1);
      lcd.print("Las huellas NO ");
      lcd.setCursor(0, 2);
      lcd.print(" Coinciden");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      lcd.clear();
      lcd.setCursor(0, 1);
      lcd.print("Las huellas NO ");
      lcd.setCursor(0, 2);
      lcd.print(" Coinciden");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  Serial.println("Remove finger");
  encender_led();  // Encender LED para indicarle al usuario que levante su dedo
  delay(2000);
  p = 0;
  while (p != FINGERPRINT_NOFINGER) {
    p = finger.getImage();
  }
  Serial.print("ID ");
  Serial.println(id);
  p = -1;
  Serial.println("Place same finger again");

  lcd.clear();
  lcd.setCursor(0, 1);
  lcd.print("Coloca el mismo  ");
  lcd.setCursor(0, 2);
  lcd.print(" dedo nuevamente");


  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
      case FINGERPRINT_OK:
        Serial.println("Image taken");
        break;
      case FINGERPRINT_NOFINGER:
        Serial.print(".");
        break;
      case FINGERPRINT_PACKETRECIEVEERR:
        Serial.println("Communication error");
        break;
      case FINGERPRINT_IMAGEFAIL:
        Serial.println("Imaging error");
        break;
      default:
        Serial.println("Unknown error");
        break;
    }
  }

  // OK success!

  p = finger.image2Tz(2);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      lcd.clear();
      lcd.setCursor(0, 1);
      lcd.print("Las huellas NO ");
      lcd.setCursor(0, 2);
      lcd.print(" Coinciden");

      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");

      lcd.clear();
      lcd.setCursor(0, 1);
      lcd.print("Las huellas NO ");
      lcd.setCursor(0, 2);
      lcd.print(" Coinciden");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK converted!
  Serial.print("Creating model for #");
  Serial.println(id);

  p = finger.createModel();
  if (p == FINGERPRINT_OK) {
    Serial.println("Prints matched!");
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_ENROLLMISMATCH) {
    Serial.println("Fingerprints did not match");
    return p;
  } else {
    Serial.println("Unknown error");
    return p;
  }

  Serial.print("ID ");
  Serial.println(id);
  p = finger.storeModel(id);
  if (p == FINGERPRINT_OK) {
    Serial.println("Stored!");

    transaccion_hecha = "add";  // Si se guardo la huella correctamente

  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_BADLOCATION) {
    Serial.println("Could not store in that location");
    return p;
  } else if (p == FINGERPRINT_FLASHERR) {
    Serial.println("Error writing to flash");
    return p;
  } else {
    Serial.println("Error writing to flash");
    return p;
  }
}

/* 
  Método para esperar a que llegue una huella 
  El método lo facilita la libreria <Adafruit_Fingerprint.h>
*/
uint8_t getFingerprintID() {
  uint8_t p = finger.getImage();
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image taken");
      aux = "-";
      break;
    case FINGERPRINT_NOFINGER:
      Serial.println("No finger detected");
      aux = "No finger detected";
      transaccion_hecha = "-";

      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK success!

  p = finger.image2Tz();
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK converted!
  p = finger.fingerSearch();
  if (p == FINGERPRINT_OK) {
    Serial.println("Found a print match!");
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_NOTFOUND) {
    Serial.println("Did not find a match");
    return p;
  } else {
    Serial.println("Unknown error");
    return p;
  }

  // found a match!
  Serial.print("Found ID #");
  Serial.print(finger.fingerID);
  Serial.print(" with confidence of ");
  Serial.println(finger.confidence);
  lcd.clear();
  lcd.setCursor(0, 1);
  lcd.print("  Acceso Correcto. ");
  lcd.setCursor(0, 2);
  lcd.print("     Gracias!!");
  return finger.fingerID;
}

// returns -1 if failed, otherwise returns ID #
int getFingerprintIDez() {
  uint8_t p = finger.getImage();
  if (p != FINGERPRINT_OK) return -1;

  p = finger.image2Tz();
  if (p != FINGERPRINT_OK) return -1;

  p = finger.fingerFastSearch();
  if (p != FINGERPRINT_OK) return -1;

  // found a match!
  Serial.print("Found ID #");
  Serial.print(finger.fingerID);  // Encontro una heulla que esta registrada
  Serial.print(" with confidence of ");
  Serial.println(finger.confidence);
  return finger.fingerID;
  transaccion_hecha = "-";  // Si se guardo

  lcd.clear();
  lcd.setCursor(0, 1);
  lcd.print("  Acceso Correcto. ");
  lcd.setCursor(0, 2);
  lcd.print("     Gracias!!");
}

/*
  Método para encender un LED para indicarle al usuario que si se detecto su huella 
*/
void encender_led() {
  digitalWrite(LED, HIGH);
  delay(2500);
  digitalWrite(LED, LOW);
  delay(500);
  digitalWrite(LED, HIGH);
  delay(1000);
  digitalWrite(LED, LOW);
  delay(500);
}
/* Funcion para mostrar texto corredizo */ 
void scrollMessage(int row, String message, int delayTime, int totalColumns) {
  for (int i = 0; i < totalColumns; i++) {
    message = " " + message;
  }
  message = message + " ";
  for (int position = 0; position < message.length(); position++) {
    lcd.setCursor(0, row);
    lcd.print(message.substring(position, position + totalColumns));
    delay(delayTime);
  }
}

/* Funcion para separar y mostrar los avisos en pantalla */ 
void separar_aviso(String aviso, int nu_avisos) {
  int a = 0;
  int tiempo = 0;
  String Titulo = "";
  String descripcion = "";
  String caracter = "";
  String inicio = "";
  String fin = "";

  String scrollingMessage = aviso;

  while (a < nu_avisos) {
    lcd.clear();
    int leng_aviso = scrollingMessage.indexOf('$');  // contar espacios hasta el $
    int ciclo_2 = 0;
    String aviso_aux = scrollingMessage.substring(0, leng_aviso);
    Serial.print("\n ");

    while (ciclo_2 == 0) {
      int cont_titulo = aviso_aux.indexOf('#');                              // contar espacios hasta el #
      Titulo = aviso_aux.substring(0, cont_titulo);                          // Extraemos el titulo
      aviso_aux = aviso_aux.substring(cont_titulo + 1, aviso_aux.length());  // Actualizamos la cadena
      cont_titulo = aviso_aux.indexOf('#');                                  // contar espacios hasta el #
      descripcion = aviso_aux.substring(0, cont_titulo);                     // Extraemos la descripcion
      aviso_aux = aviso_aux.substring(cont_titulo + 1, aviso_aux.length());  // Actualizamos la cadena
      cont_titulo = aviso_aux.indexOf('#');                                  // contar espacios hasta el #
      inicio = aviso_aux.substring(0, cont_titulo);                          // Extraemos la fecha de inicio
      aviso_aux = aviso_aux.substring(cont_titulo + 1, aviso_aux.length());  // Actualizamos la cadena
      cont_titulo = aviso_aux.indexOf('#');                                  // contar espacios hasta el #
      fin = aviso_aux.substring(0, cont_titulo);                             // Extraemos el titulo
      aviso_aux = aviso_aux.substring(cont_titulo + 1, aviso_aux.length());  // Actualizamos la cadena
      caracter = aviso_aux;
      // cont_titulo = scrollingMessage.substring(0, cont_titulo);                    // Extraemos el titulo


      ciclo_2 = 1;
    }
    // lcd.clear();

    Serial.print("\n ");
    Serial.print("\n Titulo: ");
    Serial.print(Titulo);

    String aux_inicio = inicio.substring(5, inicio.length());
    String aux_fin = fin.substring(5, fin.length());
    if (Titulo.length() < 20) {
      // Titulo
      lcd.setCursor(0, 0);
      lcd.print(Titulo);
      if (descripcion.length() < 20) {
        // descripcion
        lcd.setCursor(0, 1);
        lcd.print(descripcion);
        lcd.setCursor(0, 2);
        // fechas
        lcd.print("Fechas: ");
        lcd.setCursor(9, 2);
        lcd.print(aux_inicio);
        lcd.setCursor(15, 2);
        lcd.print(aux_fin);
        // carecter
        lcd.setCursor(5, 3);
        lcd.print(caracter);


      } else {
        int i = 0;
        tiempo = 1;
        while (i < 3) {
          // Fechas
          lcd.setCursor(0, 2);
          lcd.print("Fechas: ");
          lcd.setCursor(9, 2);
          lcd.print(aux_inicio);
          lcd.setCursor(15, 2);
          lcd.print(aux_fin);
          // carecter
          lcd.setCursor(5, 3);
          lcd.print(caracter);
          // Descripcion
          scrollMessage(1, descripcion, 600, 20);

          i++;
        }
      }

    } else {
      int s = 0;
      while (s < 3) {
        tiempo = 1;
        if (descripcion.length() < 20) {
          // Descropcion
          lcd.setCursor(0, 1);
          lcd.print(descripcion);
          // fechas
          lcd.setCursor(0, 2);
          lcd.print("Fechas: ");
          lcd.setCursor(9, 2);
          lcd.print(aux_inicio);
          lcd.setCursor(15, 2);
          lcd.print(aux_fin);
          // caracter
          lcd.setCursor(5, 3);
          lcd.print(caracter);
          // Titulo
          scrollMessage(0, Titulo, 600, 20);
        } else {
          int i = 0;
          tiempo = 1;

          while (i < 3) {
            // fechas
            lcd.setCursor(0, 2);
            lcd.print("Fechas: ");
            lcd.setCursor(9, 2);
            lcd.print(aux_inicio);
            lcd.setCursor(15, 2);
            lcd.print(aux_fin);
            // caracter
            lcd.setCursor(5, 3);
            lcd.print(caracter);
            // Titulo
            scrollMessage(0, Titulo, 600, 20);
            // descripcion
            scrollMessage(1, descripcion, 600, 20);
            i++;
          }
        }
        s++;
      }  // 1er while
    }

    if (tiempo == 0) {
      delay(30000);  // segundos para ver el recordatorio
    } else {
      delay(10000);  // segundos para ver el recordatorio
    }
    // Actualizamos la cadena para el siguiente aviso
    scrollingMessage = scrollingMessage.substring(leng_aviso + 1, scrollingMessage.length());

    a++;  // Control de avisos
  }
}
