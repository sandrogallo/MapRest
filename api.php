<?php

/*
** api.php - File principale dell'applicazione
*/

session_start();

// Inizializza la cronologia JSON se non esiste
if (!isset($_SESSION['json_history'])) {
    $_SESSION['json_history'] = [];
}

// Funzione per registrare i JSON nella cronologia di sessione
function logJsonHistory($json) {
    // Limita la cronologia a 10 elementi
    if (count($_SESSION['json_history']) >= 10) {
        array_shift($_SESSION['json_history']);
    }
    $_SESSION['json_history'][] = $json;
}

// API endpoint per ricevere i dati degli avatar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['api']) && $_GET['api'] === 'update') {
    header('Content-Type: application/json');
    
    // Leggi il corpo della richiesta
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Controlla se i dati sono validi
    if ($data !== null) {
        // Qui puoi salvare i dati su un database o un file
        // Per semplicità, li salviamo in sessione
        $_SESSION['avatar_data'] = $data;
        
        // Registra il JSON nella cronologia
        logJsonHistory($json);
        
        echo json_encode(['success' => true, 'message' => 'Dati salvati con successo']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Dati non validi']);
    }
    exit;
}

// API endpoint per ottenere i dati degli avatar
if (isset($_GET['api']) && $_GET['api'] === 'get') {
    header('Content-Type: application/json');
    
    if (isset($_SESSION['avatar_data'])) {
        echo json_encode($_SESSION['avatar_data']);
    } else {
        echo json_encode([]);
    }
    exit;
}

// API endpoint per ottenere la cronologia JSON
if (isset($_GET['api']) && $_GET['api'] === 'history') {
    header('Content-Type: application/json');
    echo json_encode($_SESSION['json_history']);
    exit;
}

// Svuota la cronologia JSON
if (isset($_GET['api']) && $_GET['api'] === 'clear_history') {
    $_SESSION['json_history'] = [];
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

