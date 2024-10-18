# bolsaempleo-backend

## Descripción
**bolsaempleo-backend** es un API basado en Slim 4.0 con PHP 8 para la creación de Web Service Rest para el consumo tanto del portal de Ofertas Laborales como el BackOffice de la Bolsa de Empleo.

## Tecnologías Utilizadas
- **Framework:** Slim 4
- **Lenguaje:** PHP 8
- **Base de Datos:** Mysql 5.4

## Instalación y Configuración

### Pasos para la instalación
1. Instalar composer https://getcomposer.org/doc/00-intro.md
2. Clona este repositorio en tu máquina local:
```bash
https://github.com/epicoguayaquil/bolsaempleo-backend.git
```
4. Abre el Terminal de comandos
5. Para descargar las dependencias navega a la raiz del proyecto y ejecuta:
```bash
composer install
```
## Estructura del Proyecto
Este proyecto esta desarrollado en un modelo de capas estructurado de la siguiente forma:
- src
	- modulo
		- BusinessLogic
		- Controllers
		- Models
		- Validators
  - routes (en esta carpeta van todas las rutas de cada modulo lleva el mismo nombre del moduo)
    - empleabilidad.php
  - dependencies.php
  - environment.php
  - middleware.php
  - routes.php
  - settings.php
**Tener en cuenta lo siguiente:**
- En caso de agregar un nuevo archivo.php en la carpeta routes debes agregar en el archivo routes.php
- Todas las calses de la carpeta BusinessLogic extienden a BaseBusinessLogic.php
- Todas las clases de la carpeta Controllers extienden a BaseControllers.php
- Todas las clases de la carpeta Models extienden a BaseModel.php
- Todas las clases de la carpeta Validators extienden a BaseValidator.php


## Cómo contribuir a este proyecto
Aquí hay 2 pasos rápidos y sencillos para contribuir a este proyecto:

* Identifica la tarea a solucionar, localizada en la pestaña **Issues**
* Añade tu nombre al archivo `CONTRIBUTORS.md`

¡Haz una solicitud de extracción (pull request) para tu trabajo y espera a que sea fusionada!

## ¡Empezamos!
* Haz un fork de este repositorio (Haz clic en el botón Fork en la parte superior derecha de esta página)
* Clona tu fork en tu máquina local

```markdown
https://github.com/epicoguayaquil/bolsaempleo-backend.git
```

* Crea una rama

```markdown
git checkout -b dev/nombre-de-la-rama
```

* Haz tus cambios (elige cualquiera de las tareas en la pestaña **Issues**)
* Haz commit y push

```markdown
git add .
git commit -m 'Mensaje del commit'
git push origin dev/nombre-de-la-rama
```
* Crea una nueva solicitud de extracción desde tu repositorio forkeado (Haz clic en el botón `New Pull Request` ubicado en la parte superior de tu repositorio)
* ¡Espera la revisión de tu PR y la aprobación de la fusión!
* __¡Dale una estrella a este repositorio__ si te ha gustado!

## Autor o Equipo de Desarrollo
EPICO

## Contacto
Para consultas relacionadas con el proyecto, puedes contactar a:
- **Ernesto Ruales**  
  Email: [ernesto.ruales@epico.gob.ec](mailto:ernesto.ruales@epico.gob.ec)

