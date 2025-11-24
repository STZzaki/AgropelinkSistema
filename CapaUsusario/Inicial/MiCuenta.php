<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Agropelink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #53ad57 0%, #29b69b 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header Styles */
        header {
            background: white;
            border-radius: 10px;
            padding: 15px 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo-icon {
            background: #53ad57;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #53ad57;
            letter-spacing: 1px;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav li {
            margin-left: 20px;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s;
            padding: 8px 12px;
            border-radius: 5px;
        }

        nav a:hover {
            color: #53ad57;
            background: #f5f5f5;
        }

        /* Account Section Styles */
        .account-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .account-header {
            background: #f9f9f9;
            padding: 25px 30px;
            border-bottom: 1px solid #eee;
        }

        .account-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .account-content {
            display: flex;
            min-height: 500px;
        }

        .account-sidebar {
            flex: 0 0 250px;
            background: #f9f9f9;
            padding: 20px 0;
            border-right: 1px solid #eee;
        }

        .account-sidebar ul {
            list-style: none;
        }

        .account-sidebar li {
            padding: 15px 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 15px;
            display: flex;
            align-items: center;
            border-left: 3px solid transparent;
        }

        .account-sidebar li i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            color: #666;
        }

        .account-sidebar li:hover {
            background: #f0f0f0;
        }

        .account-sidebar li.active {
            background: white;
            border-left-color: #53ad57;
            color: #53ad57;
            font-weight: 600;
        }

        .account-sidebar li.active i {
            color: #53ad57;
        }

        .account-main {
            flex: 1;
            padding: 30px;
        }

        .account-section {
            display: none;
        }

        .account-section.active {
            display: block;
        }

        .welcome-message {
            margin-bottom: 25px;
        }

        .welcome-message h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 10px;
        }

        .welcome-message p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .google-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: white;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
            width: 100%;
            max-width: 300px;
        }

        .google-login:hover {
            background: #f9f9f9;
            border-color: #53ad57;
        }

        .google-login i {
            color: #DB4437;
            font-size: 18px;
        }

        /* Orders Section */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .orders-table th, .orders-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .orders-table th {
            background: #f9f9f9;
            font-weight: 600;
            color: #555;
        }

        .order-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-completed {
            background: #e6f7ee;
            color: #0d8b5e;
        }

        .status-pending {
            background: #fff8e6;
            color: #d6a10e;
        }

        .status-processing {
            background: #e6f2ff;
            color: #2d6cdf;
        }

        /* Downloads Section */
        .downloads-list {
            margin-top: 20px;
        }

        .download-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .download-info h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .download-info p {
            color: #666;
            font-size: 14px;
        }

        .download-btn {
            background: #53ad57;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }

        .download-btn:hover {
            background: #29b69b;
        }

        /* Addresses Section */
        .addresses-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .address-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            position: relative;
        }

        .address-card.default {
            border-color: #53ad57;
            background: #f9fff9;
        }

        .address-type {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #53ad57;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }

        .address-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .address-btn {
            background: none;
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }

        .address-btn.edit:hover {
            background: #e6f2ff;
            border-color: #2d6cdf;
            color: #2d6cdf;
        }

        .address-btn.delete:hover {
            background: #ffe6e6;
            border-color: #e02d2d;
            color: #e02d2d;
        }

        /* Payment Methods Section */
        .payment-methods {
            margin-top: 20px;
        }

        .payment-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .payment-icon {
            width: 40px;
            height: 40px;
            background: #f5f5f5;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
            color: #555;
        }

        .payment-details {
            flex: 1;
        }

        .payment-details h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .payment-details p {
            color: #666;
            font-size: 14px;
        }

        .payment-actions {
            display: flex;
            gap: 10px;
        }

        /* Account Details Section */
        .account-form {
            max-width: 600px;
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
        }

        .form-group input:focus {
            border-color: #53ad57;
            outline: none;
            box-shadow: 0 0 0 2px rgba(83, 173, 87, 0.2);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-primary {
            background: #53ad57;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #29b69b;
        }

        .btn-secondary {
            background: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #e9e9e9;
        }

        /* Logout Section */
        .logout-content {
            text-align: center;
            padding: 40px 0;
        }

        .logout-icon {
            font-size: 60px;
            color: #53ad57;
            margin-bottom: 20px;
        }

        .logout-content h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        .logout-content p {
            color: #666;
            max-width: 500px;
            margin: 0 auto 25px;
            line-height: 1.6;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 15px;
            }

            .logo {
                margin-bottom: 15px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            nav li {
                margin: 5px;
            }

            .account-content {
                flex-direction: column;
            }

            .account-sidebar {
                flex: 1;
                border-right: none;
                border-bottom: 1px solid #eee;
            }

            .account-sidebar ul {
                display: flex;
                overflow-x: auto;
                padding-bottom: 10px;
            }

            .account-sidebar li {
                white-space: nowrap;
                border-left: none;
                border-bottom: 3px solid transparent;
            }

            .account-sidebar li.active {
                border-left-color: transparent;
                border-bottom-color: #53ad57;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="logo-text">Agropelink</div>
            </div>
            <nav>
                <ul>
                    <li><a href="#">Productos</a></li>
                    <li><a href="#">Agricultores</a></li>
                    <li><a href="#">Verduras</a></li>
                    <li><a href="#">Frutas</a></li>
                    <li><a href="#">Frutos secos</a></li>
                    <li><a href="#">Nuestra inspiración</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>
        </header>

        <div class="account-container">
            <div class="account-header">
                <h1>Mi cuenta</h1>
            </div>
            
            <div class="account-content">
                <div class="account-sidebar">
                    <ul>
                        <li class="active" data-section="dashboard">
                            <i class="fas fa-home"></i> Escritorio
                        </li>
                        <li data-section="orders">
                            <i class="fas fa-shopping-bag"></i> Pedidos
                        </li>
                        <li data-section="downloads">
                            <i class="fas fa-download"></i> Descargas
                        </li>
                        <li data-section="addresses">
                            <i class="fas fa-map-marker-alt"></i> Direcciones
                        </li>
                        <li data-section="payments">
                            <i class="fas fa-credit-card"></i> Métodos de pago
                        </li>
                        <li data-section="details">
                            <i class="fas fa-user"></i> Detalles de la cuenta
                        </li>
                        <li data-section="logout">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </li>
                    </ul>
                </div>
                
                <div class="account-main">
                    <!-- Dashboard Section -->
                    <div class="account-section active" id="dashboard">
                        <div class="welcome-message">
                            <h2>Hola <strong>usuario</strong> (¿no eres <strong>usuario</strong>? <a href="#" style="color: #53ad57; text-decoration: none;">Cerrar sesión</a>)</h2>
                            <p>Desde el escritorio de tu cuenta puedes ver tus pedidos recientes, gestionar tus direcciones de envío y facturación y editar tu contraseña y los detalles de tu cuenta.</p>
                        </div>
                        
                        <button class="google-login">
                            <i class="fab fa-google"></i>
                            Iniciar sesión con Google
                        </button>
                    </div>
                    
                    <!-- Orders Section -->
                    <div class="account-section" id="orders">
                        <h2>Pedidos</h2>
                        <p>Aquí puedes ver tu historial de pedidos y su estado actual.</p>
                        
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#12345</td>
                                    <td>15 Oct 2025</td>
                                    <td><span class="order-status status-completed">Completado</span></td>
                                    <td>€45.90</td>
                                    <td><a href="#" style="color: #53ad57;">Ver</a></td>
                                </tr>
                                <tr>
                                    <td>#12344</td>
                                    <td>10 Oct 2025</td>
                                    <td><span class="order-status status-processing">Procesando</span></td>
                                    <td>€32.50</td>
                                    <td><a href="#" style="color: #53ad57;">Ver</a></td>
                                </tr>
                                <tr>
                                    <td>#12343</td>
                                    <td>5 Oct 2025</td>
                                    <td><span class="order-status status-pending">Pendiente</span></td>
                                    <td>€67.80</td>
                                    <td><a href="#" style="color: #53ad57;">Ver</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Downloads Section -->
                    <div class="account-section" id="downloads">
                        <h2>Descargas</h2>
                        <p>Tus productos digitales disponibles para descargar.</p>
                        
                        <div class="downloads-list">
                            <div class="download-item">
                                <div class="download-info">
                                    <h3>Guía de Cultivo Sostenible</h3>
                                    <p>Descargado por última vez: 12 Oct 2025</p>
                                </div>
                                <button class="download-btn">Descargar</button>
                            </div>
                            <div class="download-item">
                                <div class="download-info">
                                    <h3>Catálogo de Productos Orgánicos</h3>
                                    <p>Descargado por última vez: 5 Oct 2025</p>
                                </div>
                                <button class="download-btn">Descargar</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Addresses Section -->
                    <div class="account-section" id="addresses">
                        <h2>Direcciones</h2>
                        <p>Gestiona tus direcciones de envío y facturación.</p>
                        
                        <div class="addresses-container">
                            <div class="address-card default">
                                <span class="address-type">Principal</span>
                                <h3>Dirección de Envío</h3>
                                <p>Usuario Ejemplo</p>
                                <p>Calle Principal, 123</p>
                                <p>28001 Madrid, España</p>
                                <div class="address-actions">
                                    <button class="address-btn edit">Editar</button>
                                    <button class="address-btn delete">Eliminar</button>
                                </div>
                            </div>
                            <div class="address-card">
                                <h3>Dirección de Facturación</h3>
                                <p>Usuario Ejemplo</p>
                                <p>Avenida Secundaria, 456</p>
                                <p>28002 Madrid, España</p>
                                <div class="address-actions">
                                    <button class="address-btn edit">Editar</button>
                                    <button class="address-btn delete">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Methods Section -->
                    <div class="account-section" id="payments">
                        <h2>Métodos de Pago</h2>
                        <p>Gestiona tus métodos de pago guardados.</p>
                        
                        <div class="payment-methods">
                            <div class="payment-card">
                                <div class="payment-icon">
                                    <i class="fab fa-cc-visa"></i>
                                </div>
                                <div class="payment-details">
                                    <h3>Visa terminada en 4321</h3>
                                    <p>Expira: 12/2026</p>
                                </div>
                                <div class="payment-actions">
                                    <button class="address-btn edit">Editar</button>
                                    <button class="address-btn delete">Eliminar</button>
                                </div>
                            </div>
                            <div class="payment-card">
                                <div class="payment-icon">
                                    <i class="fab fa-cc-paypal"></i>
                                </div>
                                <div class="payment-details">
                                    <h3>PayPal</h3>
                                    <p>usuario@ejemplo.com</p>
                                </div>
                                <div class="payment-actions">
                                    <button class="address-btn edit">Editar</button>
                                    <button class="address-btn delete">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Details Section -->
                    <div class="account-section" id="details">
                        <h2>Detalles de la Cuenta</h2>
                        <p>Edita la información de tu cuenta y cambia tu contraseña.</p>
                        
                        <form class="account-form">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" id="name" value="Usuario">
                            </div>
                            <div class="form-group">
                                <label for="lastname">Apellidos</label>
                                <input type="text" id="lastname" value="Ejemplo">
                            </div>
                            <div class="form-group">
                                <label for="email">Correo electrónico</label>
                                <input type="email" id="email" value="usuario@ejemplo.com">
                            </div>
                            <div class="form-group">
                                <label for="password">Contraseña actual (dejar en blanco para no cambiar)</label>
                                <input type="password" id="password">
                            </div>
                            <div class="form-group">
                                <label for="new-password">Nueva contraseña (dejar en blanco para no cambiar)</label>
                                <input type="password" id="new-password">
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-primary">Guardar cambios</button>
                                <button type="button" class="btn-secondary">Cancelar</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Logout Section -->
                    <div class="account-section" id="logout">
                        <div class="logout-content">
                            <div class="logout-icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <h2>¿Estás seguro de que quieres cerrar sesión?</h2>
                            <p>Serás redirigido a la página de inicio de sesión. Podrás volver a acceder a tu cuenta en cualquier momento.</p>
                            <div class="form-actions">
                                <button type="button" class="btn-primary">Cerrar sesión</button>
                                <button type="button" class="btn-secondary">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.account-sidebar li');
            const sections = document.querySelectorAll('.account-section');
            
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    menuItems.forEach(i => i.classList.remove('active'));
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Hide all sections
                    sections.forEach(section => section.classList.remove('active'));
                    
                    // Show the selected section
                    const sectionId = this.getAttribute('data-section');
                    document.getElementById(sectionId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>