
---

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

---

## ⚙️ Instalación y Ejecución Local

```bash
composer require laravel/breeze --dev
npm install
npm run dev
php artisan migrate
php artisan db:seed
```

---

## 📌 Endpoints Disponibles

### 1. 🔧 Consulta Manual

Consulta y guarda las cotizaciones actuales de **dólar oficial**, **blue** y **MEP**.

* **Método:** `GET`
* **URL:** `http://localhost:8000/api/consultar-manual`

---

### 2. 💱 Conversión de Dólares

Convierte dólares a pesos según el tipo seleccionado.

* **Método:** `GET`
* **URL:** `http://localhost:8000/api/convertir`
* **Parámetros:**

  * `valor`: Monto en USD (ej: `100`)
  * `tipo`: `oficial`, `blue`, `mep`

📌 **Ejemplo:**
`http://localhost:8000/api/convertir?valor=100&tipo=blue`

---

### 3. 📊 Promedio Mensual

Obtiene el promedio mensual de compra o venta.

* **Método:** `GET`
* **URL:** `http://localhost:8000/api/promedio`
* **Parámetros obligatorios:**

  * `tipo`: `oficial`, `blue`, `mep`
  * `valor`: `compra`, `venta`
  * `mes`: En formato `YYYY-MM` (ej: `2024-09`)

📌 **Ejemplo:**
`http://localhost:8000/api/promedio?tipo=blue&valor=venta&mes=2024-09`

---

### 4. 📈 Historial Completo

Devuelve todas las cotizaciones guardadas.

* **Método:** `GET`
* **URL:** `http://localhost:8000/api/historial`

---

### 5. 🔍 Historial con Filtros

Filtra el historial por tipo y rango de fechas.

* **Método:** `GET`
* **URL:** `http://localhost:8000/api/historial`
* **Parámetros opcionales:**

  * `tipo`: `oficial`, `blue`, `mep`
  * `desde`: Fecha inicio (`YYYY-MM-DD`)
  * `hasta`: Fecha fin (`YYYY-MM-DD`)

📌 **Ejemplo:**
`http://localhost:8000/api/historial?tipo=blue&desde=2024-09-01&hasta=2024-09-30`

---

## 🧪 Orden Recomendado para Pruebas

1. **Ejecutar consulta manual**
   `GET http://localhost:8000/api/consultar-manual`

2. **Verificar que se guardaron los datos**
   `GET http://localhost:8000/api/historial`

3. **Probar conversión de dólares**
   `GET http://localhost:8000/api/convertir?valor=100&tipo=blue`

4. **Probar promedio mensual**
   `GET http://localhost:8000/api/promedio?tipo=blue&valor=venta&mes=2024-09`

---

## 📚 Tecnologías Usadas

* Laravel
* Laravel Breeze
* MySQL
* Apis

## Colaboradores
* Jennifer Elizabeth Coronel 
* Facundo Nahuel Espinola
---
