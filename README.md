# Prueba técnica para Backend o Fullstack

En este repositorio se va a dar la respuesta al reto propuesto por el empleador. A continuación se detallan los pasos a seguir para la ejecución del proyecto y la documentación de los endpoints.

## Requisitos

- Tener instalado Docker en tu equipo.
- Tener clonado este repositorio.
- Tener instalado Composer en tu equipo. (No quise modificar el Dockerfile)

## Ejecución

Para la ejecución del proyecto se deben seguir los siguientes pasos:

1. Clonar el repositorio
2. Ingresar al directorio del proyecto
3. Ejecutar el siguiente comando:

```bash
docker compose up -d --build
```

4. Esperar a que finalice el proceso de instalación.
5. Ejecutar el siguiente comando para instalar las dependencias de PHP:

```bash
composer install
```

## Documentación de los endpoints

Todos los endpoints cuentan con un prefijo `/api/v1` para diferenciarlos de los archivos estáticos que puedan añadirse en el futuro y para tener las API versionadas.

Se dividieron los endpoints en dos controladores, uno para los usuarios y otro para los comentarios. A continuación se detallan los endpoints de cada controlador.

### Base URL

- **URL:** `http://localhost:8080/api/v1`

### Usuarios

#### Crear un usuario

- **URI:** `/api/v1/users`
- **Método:** `POST`
- **Descripción:** Crea un nuevo usuario en la base de datos.
- **Parámetros de consulta (Query Params):** No aplica.

- **Cuerpo de la solicitud (Body):**
  - `fullname` (string): Nombre del usuario.
  - `email` (string): Correo electrónico del usuario.
  - `pass` (string): Contraseña del usuario.
  - `openid` (string): Identificador único del usuario.
- **Respuesta exitosa:**
  - **Código:** `201 Created`
  - **Contenido:**

```json
{
  "status": 201,
  "data": {
    "id": 9,
    "fullname": "Random User",
    "email": "test6@test.io",
    "openid": "randomUniqueId6",
    "creation_date": "2024-04-14 02:09:42",
    "update_date": "2024-04-14 02:09:42"
  }
}
```

**_Nota:_** El campo `pass` no se muestra en la respuesta por seguridad. Esto es extensible a todos los campos que puedan contener información sensible.

- **Respuesta fallida:**
  - **Código:** `422 Unprocessable Entity`
  - **Contenido:**

```json
{
  "error": {
    "status": 422,
    "message": "Validation errors",
    "errors": {
      "fullname": ["fullname is required"]
    }
  }
}
```

**_Nota:_** El error se muestra en el campo `errors` y se detalla el campo que falló y el mensaje de error.

#### Eliminar un usuario

- **URI:** `/api/v1/users/{id}`
- **Método:** `DELETE`
- **Descripción:** Elimina un usuario de la base de datos.
- **Parámetros de consulta (Query Params):**

  - `id` (int): ID del usuario a eliminar.

- **Respuesta exitosa:**

  - **Código:** `204 No Content`
  - **Contenido:** No hay contenido en la respuesta.

- **Respuesta fallida:**
  - **Código:** `404 Not Found`
  - **Contenido:**

```json
{
  "status": 404,
  "message": "Resource not found"
}
```

#### Actualizar un usuario

- **URI:** `/api/v1/users/{id}`
- **Método:** `PUT`
- **Descripción:** Actualiza la información de un usuario en la base de datos.
- **Parámetros de consulta (Query Params):**

  - `id` (int): ID del usuario a actualizar.

- **Cuerpo de la solicitud (Body):**

  - `fullname` (string): Nombre del usuario.
  - `email` (string): Correo electrónico del usuario.
  - `pass` (string): Contraseña del usuario.
  - `openid` (string): Identificador único del usuario.

- **Respuesta exitosa:**
  - **Código:** `200 OK`
  - **Contenido:**
  ```json
  {
    "status": 200,
    "data": {
      "id": 9,
      "fullname": "Random User",
      "email": "test@test.io",
      "openid": "randomUniqueId",
      "creation_date": "2024-04-14 02:09:42",
      "update_date": "2024-04-14 02:09:42"
    }
  }
  ```
- **Respuesta fallida:**
  - **Código:** `404 Not Found`
  - **Contenido:**
  ```json
  {
    "status": 404,
    "message": "Resource not found"
  }
  ```
  - **Código:** `422 Unprocessable Entity`
    - **Contenido:**
    ```json
    {
      "error": {
        "status": 422,
        "message": "Validation errors",
        "errors": {
          "openid": ["openid must be unique"]
        }
      }
    }
    ```

#### Conseguir la información de un usuario

- **URI:** `/api/v1/users/{id}`
- **Método:** `GET`
- **Descripción:** Obtiene la información de un usuario de la base de datos.
- **Parámetros de consulta (Query Params):**

  - `id` (int): ID del usuario a obtener.

- **Respuesta exitosa:** - **Código:** `200 OK` - **Contenido:**

  ```json
  {
    "status": 200,
    "data": {
      "id": 9,
      "fullname": "Random User",
      "email": "test@test.io",
      "openid": "randomUniqueId",
      "creation_date": "2024-04-14 02:09:42",
      "update_date": "2024-04-14 02:09:42"
    }
  }
  ```

- **Respuesta fallida:**

  - **Código:** `404 Not Found`
  - **Contenido:**

  ```json
  {
    "status": 404,
    "message": "Resource not found"
  }
  ```

### Comentarios

#### Crear un comentario

- **URI:** `/api/v1/users/{userId}/comments`
- **Método:** `POST`
- **Descripción:** Crea un nuevo comentario en la base de datos.
- **Parámetros de consulta (Query Params):** - `userId` (int): ID del usuario que realiza el comentario.

- **Cuerpo de la solicitud (Body):**

  - `comment` (string): Comentario del usuario.
  - `likes` (int): Número de likes del comentario.

- **Respuesta exitosa:**
  - **Código:** `201 Created`
  - **Contenido:**

```json
{
  "status": 201,
  "data": {
    "id": 3,
    "user": 1,
    "coment_text": "This is my second comment too! Hurray!",
    "likes": 897,
    "creation_date": "2024-04-13 22:28:49",
    "update_date": "2024-04-13 22:28:49"
  }
}
```

- **Respuesta fallida:**
  - **Código:** `422 Unprocessable Entity`
  - **Contenido:**

```json
{
  "error": {
    "status": 422,
    "message": "Validation errors",
    "errors": {
      "comment": ["comment is required"]
    }
  }
}
```

#### Eliminar un comentario

- **URI:** `/api/v1/comments/{id}`

- **Método:** `DELETE`
- **Descripción:** Elimina un comentario de la base de datos.
- **Parámetros de consulta (Query Params):**

  - `id` (int): ID del comentario a eliminar.

- **Respuesta exitosa:**

      - **Código:** `204 No Content`
      - **Contenido:** No hay contenido en la respuesta.

- **Respuesta fallida:**
  - **Código:** `404 Not Found`
  - **Contenido:**
  ```json
  {
    "status": 404,
    "message": "Resource not found"
  }
  ```

#### Actualizar un comentario

- **URI:** `/api/v1/comments/{id}`
- **Método:** `PUT`
- **Descripción:** Actualiza la información de un comentario en la base de datos.
- **Parámetros de consulta (Query Params):**

  - `id` (int): ID del comentario a actualizar.

- **Cuerpo de la solicitud (Body):**

      - `comment` (string): Comentario del usuario.
      - `likes` (int): Número de likes del comentario.

- **Respuesta exitosa:**

  - **Código:** `200 OK`
  - **Contenido:**
    ```json
    {
      "status": 200,
      "data": {
        "id": 3,
        "user": 1,
        "coment_text": "This is my second comment too! Hurray!",
        "likes": 897,
        "creation_date": "2024-04-13 22:28:49",
        "update_date": "2024-04-13 22:28:49"
      }
    }
    ```

- **Respuesta fallida:**
  - **Código:** `404 Not Found`
  - **Contenido:**
    ```json
    {
      "status": 404,
      "message": "Resource not found"
    }
    ```
  - **Código:** `422 Unprocessable Entity`
    - **Contenido:**
    ```json
    {
      "error": {
        "status": 422,
        "message": "Validation errors",
        "errors": {
          "likes": ["likes must be a number"]
        }
      }
    }
    ```

#### Conseguir la información de un comentario

- **URI:** `/api/v1/comments/{id}`
- **Método:** `GET`
- **Descripción:** Obtiene la información de un comentario de la base de datos.
- **Parámetros de consulta (Query Params):**

  - `id` (int): ID del comentario a obtener.

- **Respuesta exitosa:** - **Código:** `200 OK` - **Contenido:**

  ```json
  {
    "status": 200,
    "data": {
      "id": 3,
      "user": 1,
      "coment_text": "This is my second comment too! Hurray!",
      "likes": 897,
      "creation_date": "2024-04-13 22:28:49",
      "update_date": "2024-04-13 22:28:49"
    }
  }
  ```

- **Respuesta fallida:**

  - **Código:** `404 Not Found`
  - **Contenido:**

  ```json
  {
    "status": 404,
    "message": "Resource not found"
  }
  ```

## Notas adicionales

- Se utilizó un query builder para la creación de las consultas SQL, es un sistema muy burdo pero funcional para la prueba.

- Se utilizó un sistema de validación de campos para los endpoints, en caso de que un campo no cumpla con las reglas establecidas se devolverá un error con el campo que falló y el mensaje de error.

- Se utilizó un sistema de manejo de errores para los endpoints, en caso de que ocurra un error en la base de datos o en el servidor se devolverá un error con el código de estado y un mensaje de error.

- Se utilizó un sistema de manejo de excepciones para los endpoints, en caso de que ocurra una excepción en el servidor se devolverá un error con el código de estado y un mensaje de error.

- Se utilizó un sistema de manejo de respuestas para los endpoints, en caso de que la solicitud sea exitosa se devolverá un mensaje de éxito con el código de estado y la información solicitada.

- Se utilizó un sistema de manejo de rutas para los endpoints, en caso de que la ruta solicitada no exista se devolverá un error con el código de estado y un mensaje de error.

## Conclusiones

El proyecto fue desarrollado en su totalidad, se crearon los endpoints solicitados y se documentaron para su fácil uso. Se utilizó un sistema de validación de campos, manejo de errores, manejo de excepciones y manejo de respuestas para los endpoints. Se utilizó un sistema de manejo de rutas para los endpoints, en caso de que la ruta solicitada no exista se devolverá un error con el código de estado y un mensaje de error.

No se realizó la interfaz opcional ya que mi fuerte es el backend y el uso de JavaScript puro no es mi fuerte. Con gusto podría hacer una solución basada en Vue o React con estilos en TailwindCSS si se requiere.

# Contenido original

## Instrucciones de la prueba

En este repositorio encontrarás todo lo necesario para completar la prueba y demostrar tus habilidades en el desarrollo de aplicaciones web del lado del servidor.

## Cómo completar la prueba

1. Lee cuidadosamente la descripción del proyecto.
2. Configura tu entorno de desarrollo local.
3. Desarrolla el proyecto utilizando las tecnologías y herramientas especificadas.
4. Sigue las instrucciones de la evaluación para entregar tu prueba.

## Consejos para completar la prueba

- Tómate tu tiempo para leer y comprender la descripción del proyecto.
- Asegúrate de tener un buen conocimiento de las tecnologías y herramientas que se te solicitan.
- Escribe código limpio, bien documentado y eficiente.
- No dudes en utilizar los recursos disponibles para ayudarte.
- Entrega tu prueba a tiempo.

## La prueba

### Objetivo

El objetivo de esta prueba técnica es evaluar tu capacidad para desarrollar un CRUD (Create, Read, Update, Delete) en PHP puro, sin utilizar frameworks. La prueba se divide en dos partes:

- **Parte 1:** Crear los endpoints para las siguientes acciones en **PHP puro**:
  - Crear un usuario
  - Eliminar un usuario
  - Actualizar un usuario
  - Conseguir la información de un usuario
  - Crear un comentario
  - Eliminar un comentario
  - Actualizar un comentario
  - Conseguir la información de un comentario
- **Parte 2:** (Opcional) Desarrollar una interfaz de usuario con las llamadas a los endpoints creados en **JavaScript puro**.

### Entorno de desarrollo

Para facilitar el desarrollo y la evaluación, hemos creado un repositorio con Docker. El proyecto incluye:

- **Imagen de Apache con PHP 8.2:** Proporciona el entorno de ejecución para el código PHP.
- **Imagen de MariaDB:** Almacena la base de datos utilizada en la aplicación.
- **Imagen de PHPMyAdmin:** Permite la gestión y visualización de la base de datos de forma gráfica.

### Acceso al proyecto

- **Navegador:** http://localhost:8080
- **Base de datos:** http://localhost:8081

### Credenciales de la base de datos:

- **Usuario:** prueba_web
- **Contraseña:** 123456
- **Nombre de la base de datos:** prueba
- **Host:** mariadb

## Instalación

### Requisitos

- Tener instalado Docker en tu equipo.
- Tener clonado este repositorio.

### Pasos

1. Accede al directorio del proyecto en tu terminal
2. Ejecuta el siguiente comando:

```bash
docker compose up -d --build
```

- La opción `--build` solo es necesaria la primera vez que inicies el proyecto.
- La opción `-d` (_dispatched_) inicia el servidor en segundo plano e ignora los logs de Docker. Es opcional.

3. Espera a que finalice el proceso de instalación.
4. Accede a `localhost:8081` en tu navegador web.
5. Utiliza las credenciales proporcionadas anteriormente para iniciar sesión.
6. Verifica que existe una base de datos llamada `prueba` con dos tablas: `user` y `user_comment`.

### Solución de errores

Si experimentas errores durante la instalación, puedes intentar lo siguiente:

1. Detén la instancia de Docker actual:

```bash
docker compose stop
```

2. Reinicia la instancia de Docker:

```bash
docker compose up -d
```

### Recursos adicionales

- Documentación de Docker: https://docs.docker.com/
- Guía de inicio rápido de Docker Compose: https://docs.docker.com/compose/gettingstarted/

## Entrega

Para entregar la prueba técnica, por favor sigue estos pasos:

1. Correo electrónico:
   - Envía tu prueba a la dirección de correo electrónico `careers@fonsecantero.com`.
   - **Asunto:** Tu nombre completo seguido de `| Prueba backend`. Ejemplo: `Miguel Mendoza López | Prueba backend`.
   - Cuerpo del mensaje:
     - Incluye el **enlace al repositorio** donde has realizado la prueba.
     - Proporciona un **dato de contacto**, ya sea tu **correo electrónico** o **teléfono**.
2. Curriculum vitae:
   - Adjunta un **curriculum vitae** actualizado a tu correo electrónico.

### Ejemplo de asunto de correo electrónico:

```
Miguel Mendoza López | Prueba backend
```

### Ejemplo de cuerpo del mensaje:

```
Hola,

En este correo electrónico envío mi prueba técnica para el puesto de desarrollador Backend.

Enlace al repositorio: https://github.com/usuario/prueba-backend

Contacto:
* Email: miguel.mendoza@correo.com
* Teléfono: 666-123-456

Muchas gracias por su tiempo y atención.

Atentamente,

Miguel Mendoza López
```

### Recomendaciones

- Asegúrate de que el asunto del correo electrónico sea correcto.
- Revisa que el enlace al repositorio funcione correctamente.
- Verifica que tu curriculum vitae esté actualizado y sea legible.

## Evaluación

La prueba se evaluará en base a los siguientes criterios:

### Requisitos

- **Corrección del código:**
  - El código debe estar bien escrito, con una estructura clara y organizada.
  - Se debe utilizar una sintaxis correcta y consistente.
  - El código debe ser eficiente y no contener errores que afecten a su rendimiento.
  - Debe cumplir con los requisitos de la prueba, incluyendo la implementación de las funcionalidades solicitadas.
- **Funcionalidad:**
  - Los endpoints deben funcionar correctamente y responder a las solicitudes de forma esperada.
  - Se debe verificar el correcto manejo de errores y la gestión de las excepciones.
  - La aplicación debe ser robusta y capaz de manejar diferentes escenarios de uso.

### Puntos extra

- **Diseño de la interfaz de usuario (opcional):**
  - Se valorará la calidad del diseño, la usabilidad y la experiencia de usuario.
  - La interfaz debe ser intuitiva, atractiva y fácil de usar.
  - Se debe tener en cuenta la accesibilidad y la capacidad de respuesta en diferentes dispositivos.
- **Implementación de login con Google:**
  - Se valorará la capacidad de integrar la aplicación con Google OAuth para permitir el login de usuarios.
  - La integración debe ser segura y seguir las mejores prácticas.
  - Debe usarse el estandar de Open Id.

### Escala de evaluación

- **Excelente:** Cumple con todos los requisitos y puntos extra de forma excepcional.
- **Bueno:** Cumple con todos los requisitos y la mayoría de los puntos extra.
- **Suficiente:** Cumple con los requisitos mínimos.
- **Insuficiente:** No cumple con los requisitos mínimos.

### Comentarios:

- Se valorará la capacidad del candidato para resolver problemas de forma creativa y eficiente.
- Se tendrá en cuenta la claridad y la documentación del código.
- Se recomienda realizar pruebas exhaustivas de la aplicación antes de entregarla.
