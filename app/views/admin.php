<?php
/**
 * Dashboard Admin - Gestion complète allergies et traitements
 * Style ECOSAVE (Vert écologique)
 * Point d'accès: http://localhost/gestion-allergies/app/views/admin.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EcoSave Gestion Carbone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f9f4;
            display: flex;
        }

        .sidebar {
            width: 240px;
            background: #2e7d32;
            color: white;
            min-height: 100vh;
            padding: 20px;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 18px;
            color: white;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .main {
            flex: 1;
            padding: 20px;
        }

        .navbar {
            background: white;
            padding: 15px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
        }

        .navbar-brand {
            color: #2e7d32;
            font-weight: bold;
            font-size: 18px;
        }

        .navbar-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .navbar-buttons a {
            color: white;
            background: #2e7d32;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .navbar-buttons a:hover {
            background: #1b5e20;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(46, 125, 50, 0.3);
        }
        

        h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .col-md-3 {
            flex: 1;
            min-width: 200px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: #2e7d32;
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-card p {
            color: #666;
            margin: 0;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background: #66bb6a;
            color: white;
            padding: 20px;
        }

        .card-header h5 {
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        .card-body.p-0 {
            padding: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead th {
            background: #f4f9f4;
            border-bottom: 2px solid #e0e0e0;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .table tbody td {
            border-bottom: 1px solid #e0e0e0;
            padding: 12px;
        }

        .table tbody tr:hover {
            background: #f9f9f9;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            margin-right: 5px;
        }

        .btn-primary {
            background: #2e7d32;
            color: white;
        }

        .btn-primary:hover {
            background: #1b5e20;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-info {
            background: #66bb6a;
            color: white;
        }

        .btn-info:hover {
            background: #2e7d32;
        }

        .btn-danger {
            background: #d32f2f;
            color: white;
        }

        .btn-danger:hover {
            background: #b71c1c;
        }

        .btn-warning {
            background: #f57c00;
            color: white;
        }

        .btn-warning:hover {
            background: #e65100;
        }

        .btn-outline-secondary {
            background: white;
            color: #666;
            border: 1px solid #ddd;
        }

        .btn-outline-secondary:hover {
            background: #f4f9f4;
        }

        .btn-secondary {
            background: #999;
            color: white;
        }

        .btn-secondary:hover {
            background: #777;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.bg-secondary {
            background: #a0aec0;
            color: white;
        }

        .badge.bg-danger {
            background: #d32f2f;
            color: white;
        }

        .badge.bg-warning {
            background: #f57c00;
            color: white;
        }

        .badge.bg-info {
            background: #0288d1;
            color: white;
        }

        .badge.bg-success {
            background: #388e3c;
            color: white;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2e7d32;
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        /* Modal de confirmation au-dessus de tous les autres */
        #modal-confirmation {
            z-index: 9999;
        }

        .modal.show {
            display: block;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: #2e7d32;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 24px;
            color: white;
            cursor: pointer;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #ddd;
        }

        .loading {
            text-align: center;
            padding: 40px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e0e0e0;
            border-top: 4px solid #2e7d32;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .page-section {
            display: none;
        }

        .page-section.active {
            display: block;
        }

        .page-indicator {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .text-end {
            text-align: right;
        }

        .row-mb-3 {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .row-mb-3 input {
            flex: 1;
        }

        .col-md-8 {
            flex: 1;
            min-width: 200px;
        }

        .col-md-4 {
            flex: 1;
            min-width: 180px;
        }
        .sortable { cursor: pointer; position: relative; }
        .sortable:after { content: ' ↕'; font-size: 0.8em; opacity: 0.5; }
        .sort-asc:after { content: ' ↑'; opacity: 1; color: #2e7d32; }
        .sort-desc:after { content: ' ↓'; opacity: 1; color: #2e7d32; }

        .stat-bar-container { background: #eee; border-radius: 10px; height: 10px; margin: 10px 0; overflow: hidden; }
        .stat-bar { height: 100%; transition: width 0.5s ease; }
        .stat-bar.bas { background: #66bb6a; }
        .stat-bar.moyen { background: #ffa726; }
        .stat-bar.élevé { background: #ef5350; }

        /* Chatbot Premium Styles */
        .chatbot-bubble {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 65px;
            height: 65px;
            background: linear-gradient(135deg, #2e7d32, #66bb6a);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 28px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(46, 125, 50, 0.4);
            z-index: 2000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .chatbot-bubble:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .chatbot-window {
            position: fixed;
            bottom: 110px;
            right: 30px;
            width: 380px;
            height: 550px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            display: none;
            flex-direction: column;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            z-index: 2000;
            overflow: hidden;
            animation: chatbotSlideIn 0.4s ease-out;
        }

        @keyframes chatbotSlideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .chatbot-header {
            background: linear-gradient(135deg, #1b5e20, #2e7d32);
            padding: 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chatbot-header h4 {
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chatbot-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 15px;
            font-size: 0.9rem;
            line-height: 1.4;
            position: relative;
        }

        .message.ai {
            align-self: flex-start;
            background: white;
            color: #333;
            border-bottom-left-radius: 2px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .message.user {
            align-self: flex-end;
            background: #2e7d32;
            color: white;
            border-bottom-right-radius: 2px;
        }

        .chatbot-input-container {
            padding: 15px;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chatbot-input-container input {
            flex: 1;
            border: none;
            outline: none;
            padding: 10px;
            font-size: 0.9rem;
        }

        .chatbot-input-container button {
            background: #2e7d32;
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .chatbot-input-container button:hover {
            background: #1b5e20;
        }

        .typing-indicator {
            padding: 5px 20px;
            display: none;
            font-style: italic;
            color: #666;
            font-size: 0.8rem;
        }

        /* Suggestion Modal Styles */
        .suggestion-card {
            background: #f1f8e9;
            border-left: 5px solid #2e7d32;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 8px 8px 0;
            transition: 0.3s;
        }
        .suggestion-card:hover { transform: translateX(5px); background: #e8f5e9; }
        .suggestion-pct { color: #2e7d32; font-weight: bold; font-size: 1.2rem; }

        /* ===== UI REFRESH (sans modification HTML) ===== */
        :root {
            --brand-1: #4f46e5;
            --brand-2: #7c3aed;
            --ink-900: #0f172a;
            --ink-700: #334155;
            --ink-500: #64748b;
            --surface: #ffffff;
            --surface-soft: #f8faff;
            --line: #e2e8f0;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink-900);
            background:
                radial-gradient(circle at 8% 12%, rgba(124, 58, 237, 0.15), transparent 35%),
                linear-gradient(145deg, #f4f7ff 0%, #eef2ff 100%);
        }

        .sidebar {
            background: linear-gradient(180deg, #1e3a8a 0%, #312e81 40%, #4f46e5 100%);
            box-shadow: 16px 0 32px rgba(15, 23, 42, 0.25);
            border-right: 1px solid rgba(255,255,255,0.15);
        }

        .sidebar h2 {
            letter-spacing: 0.3px;
            border-bottom-color: rgba(255,255,255,0.2);
        }

        .sidebar a {
            border: 1px solid transparent;
            border-radius: 10px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.22);
            transform: translateX(2px);
        }

        .navbar {
            background: rgba(255,255,255,0.86);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.7);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            border-radius: 14px;
        }

        .navbar-brand {
            color: #1e3a8a;
        }

        .navbar-buttons a,
        .btn-primary {
            background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
            box-shadow: 0 8px 16px rgba(79, 70, 229, 0.28);
        }

        .navbar-buttons a:hover,
        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca, #6d28d9);
        }

        h2 {
            color: #1e3a8a;
            letter-spacing: 0.2px;
        }

        .stat-card,
        .card,
        .modal-content {
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            background: var(--surface);
        }

        .stat-card h3 {
            color: #3730a3;
        }

        .card-header,
        .modal-header {
            background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
        }

        .table thead th {
            background: var(--surface-soft);
            color: var(--ink-700);
        }

        .table tbody tr:hover {
            background: #f5f8ff;
        }

        .table tbody td {
            color: var(--ink-700);
        }

        .btn {
            border-radius: 10px;
        }

        .btn-info {
            background: #2563eb;
        }

        .btn-warning {
            background: #f59e0b;
        }

        .btn-danger {
            background: #dc2626;
        }

        .form-control,
        .form-select {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.2);
        }

        .badge.bg-success { background: #16a34a; }
        .badge.bg-warning { background: #f59e0b; }
        .badge.bg-danger  { background: #dc2626; }

        .chatbot-bubble {
            background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.35);
        }

        /* ===== GREEN THEME + PERFORMANCE OVERRIDES ===== */
        :root {
            --green-900: #1b5e20;
            --green-800: #2e7d32;
            --green-700: #388e3c;
            --green-500: #66bb6a;
            --green-100: #e8f5e9;
            --text-900: #1f2937;
            --text-700: #4b5563;
            --line-soft: #e5e7eb;
        }

        body {
            background: #f4f9f4;
            color: var(--text-900);
        }

        .sidebar {
            background: linear-gradient(180deg, var(--green-900) 0%, var(--green-800) 60%, var(--green-700) 100%);
            box-shadow: 8px 0 18px rgba(27, 94, 32, 0.2);
            border-right: 1px solid rgba(255,255,255,0.12);
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.14);
            border-color: rgba(255,255,255,0.22);
            transform: none;
        }

        .navbar {
            background: #ffffff;
            border: 1px solid var(--line-soft);
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            backdrop-filter: none;
        }

        .navbar-brand,
        h2 {
            color: var(--green-800);
        }

        .navbar-buttons a,
        .btn-primary,
        .card-header,
        .modal-header {
            background: linear-gradient(135deg, var(--green-700), var(--green-800));
        }

        .navbar-buttons a:hover,
        .btn-primary:hover {
            background: var(--green-900);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(46, 125, 50, 0.25);
        }

        .stat-card,
        .card,
        .table-responsive,
        .modal-content {
            border: 1px solid var(--line-soft);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .stat-card h3 {
            color: var(--green-800);
        }

        .table thead th {
            background: #f0f7f0;
            color: var(--text-900);
        }

        .table tbody td {
            color: var(--text-700);
        }

        .table tbody tr:hover {
            background: #f7fbf7;
        }

        .btn {
            border-radius: 8px;
            box-shadow: none;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        }

        .btn-info { background: #43a047; }
        .btn-warning { background: #fb8c00; }
        .btn-danger { background: #d32f2f; }

        .form-control,
        .form-select {
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--green-500);
            box-shadow: 0 0 0 3px rgba(102, 187, 106, 0.22);
        }

        .badge.bg-success { background: #2e7d32; }
        .badge.bg-warning { background: #f57c00; }
        .badge.bg-danger  { background: #c62828; }

        .chatbot-bubble {
            background: linear-gradient(135deg, var(--green-700), var(--green-800));
            box-shadow: 0 8px 16px rgba(46, 125, 50, 0.3);
        }

        /* ===== ULTRA REDESIGN ===== */
        :root {
            --bg-a: #0f172a;
            --bg-b: #111827;
            --panel: rgba(255, 255, 255, 0.96);
            --panel-soft: rgba(255, 255, 255, 0.9);
            --line: #e5e7eb;
            --green-900: #14532d;
            --green-800: #166534;
            --green-700: #15803d;
            --green-500: #22c55e;
            --green-400: #4ade80;
            --text-900: #0f172a;
            --text-700: #334155;
            --text-500: #64748b;
            --radius-lg: 18px;
            --radius-md: 12px;
        }

        body {
            font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at 0% 0%, rgba(34, 197, 94, 0.16), transparent 28%),
                radial-gradient(circle at 100% 0%, rgba(74, 222, 128, 0.14), transparent 24%),
                linear-gradient(135deg, #f7fbf7 0%, #edf7ee 100%);
            color: var(--text-900);
        }

        .sidebar {
            width: 268px;
            background: linear-gradient(180deg, #0b3b22 0%, #14532d 48%, #166534 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow: 16px 0 34px rgba(2, 6, 23, 0.35);
            padding: 26px 18px;
        }

        .sidebar h2 {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            margin-bottom: 26px;
            padding-bottom: 16px;
        }

        .sidebar a {
            margin: 8px 0;
            padding: 12px 14px;
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: linear-gradient(135deg, rgba(255,255,255,0.16), rgba(255,255,255,0.1));
            border-color: rgba(255,255,255,0.2);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
        }

        .main {
            padding: 20px 22px;
        }

        .navbar {
            border-radius: var(--radius-lg);
            background: var(--panel);
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.1);
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .navbar-brand {
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: 0.2px;
            color: var(--green-800);
        }

        .navbar-buttons a {
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--green-700), var(--green-800));
            box-shadow: 0 8px 18px rgba(21, 128, 61, 0.25);
        }

        h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 18px;
            color: var(--green-800);
        }

        .row {
            gap: 16px;
        }

        .stat-card {
            border-radius: var(--radius-lg);
            padding: 26px 18px;
            border: 1px solid var(--line);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            background: linear-gradient(180deg, #ffffff 0%, #fcfffc 100%);
        }

        .stat-card h3 {
            font-size: 2.25rem;
            font-weight: 800;
            margin: 6px 0 4px;
            color: var(--green-800);
        }

        .stat-card p {
            color: var(--text-500);
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .card {
            border-radius: var(--radius-lg);
            border: 1px solid var(--line);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            background: var(--panel-soft);
        }

        .card-header {
            background: linear-gradient(135deg, var(--green-700), var(--green-800));
            padding: 16px 20px;
        }

        .card-header h5 {
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .table thead th {
            background: #f4faf4;
            color: var(--text-700);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.45px;
            border-bottom: 1px solid #d9e9dc;
        }

        .table tbody td {
            font-size: 0.95rem;
            color: #1f2937;
            border-bottom: 1px solid #edf2ef;
        }

        .table tbody tr:hover {
            background: #f8fdf8;
        }

        .btn {
            border-radius: 10px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .btn-sm {
            padding: 6px 10px;
        }

        .btn-info {
            background: linear-gradient(135deg, #16a34a, #15803d);
        }

        .btn-warning {
            background: linear-gradient(135deg, #fb923c, #ea580c);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
        }

        .form-control,
        .form-select {
            border-radius: var(--radius-md);
            border: 1px solid #d6dfd9;
            padding: 11px 13px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--green-500);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.18);
        }

        .modal-content {
            border-radius: 20px;
            border: 1px solid #dfe8e1;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #166534, #15803d);
        }

        .badge {
            border-radius: 999px;
            padding: 6px 11px;
            font-size: 0.72rem;
            letter-spacing: 0.25px;
            font-weight: 800;
        }

        .chatbot-bubble {
            width: 68px;
            height: 68px;
            background: linear-gradient(135deg, #16a34a, #15803d);
            box-shadow: 0 12px 24px rgba(22, 163, 74, 0.35);
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 236px;
            }
            .main {
                padding: 14px;
            }
            h2 {
                font-size: 1.6rem;
            }
        }

        /* ===== PREMIUM POLISH V2 ===== */
        body {
            background:
                radial-gradient(circle at 15% 15%, rgba(34, 197, 94, 0.12), transparent 30%),
                radial-gradient(circle at 90% 5%, rgba(22, 163, 74, 0.1), transparent 28%),
                linear-gradient(135deg, #f7fcf8 0%, #edf7ef 100%);
        }

        .main-content,
        .main {
            max-width: 1440px;
        }

        .navbar {
            position: sticky;
            top: 14px;
            z-index: 50;
            border-radius: 16px;
        }

        h2 {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #14532d, #16a34a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 22px;
        }

        h2::after {
            content: "";
            display: block;
            width: 56px;
            height: 4px;
            border-radius: 999px;
            margin-left: 8px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border: 1px solid #d8e6dc;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #16a34a, #4ade80);
        }

        .card {
            border: 1px solid #dce8df;
        }

        .card-header {
            position: relative;
        }

        .card-header::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 1px;
            background: rgba(255, 255, 255, 0.35);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th:first-child {
            border-top-left-radius: 10px;
        }

        .table thead th:last-child {
            border-top-right-radius: 10px;
        }

        .table tbody tr:nth-child(even) {
            background: #fbfdfb;
        }

        .table tbody tr:hover {
            background: #f2faf3;
        }

        .btn-outline-secondary {
            border-color: #c6d4ca;
            color: #2e7d32;
            background: #fff;
        }

        .btn-outline-secondary:hover {
            background: #ecf8ee;
            border-color: #66bb6a;
            color: #1b5e20;
        }

        .page-indicator span {
            font-weight: 700;
            color: #166534;
            background: #edf8ef;
            border: 1px solid #d3e6d7;
            border-radius: 10px;
            padding: 7px 10px;
        }

        /* ===== THEME GESTION CARBONE ===== */
        body {
            background:
                radial-gradient(circle at 10% 10%, rgba(34, 197, 94, 0.14), transparent 28%),
                radial-gradient(circle at 90% 0%, rgba(20, 83, 45, 0.12), transparent 24%),
                linear-gradient(140deg, #f2f7f3 0%, #e9f2ec 48%, #e3ece6 100%);
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: 0.22;
            background-image:
                linear-gradient(90deg, rgba(20, 83, 45, 0.06) 1px, transparent 1px),
                linear-gradient(rgba(20, 83, 45, 0.06) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: radial-gradient(circle at center, black 34%, transparent 100%);
        }

        .sidebar,
        .main {
            position: relative;
            z-index: 1;
        }

        .sidebar {
            background: linear-gradient(180deg, #0d3f25 0%, #14532d 50%, #166534 100%);
        }

        .navbar {
            background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(246, 252, 247, 0.96));
        }

        .card,
        .stat-card {
            background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248, 253, 249, 0.95));
        }

        /* ===== STYLE ALTERNATIF (VERT CONSERVÉ) ===== */
        body {
            background: linear-gradient(180deg, #f6fbf7 0%, #eef6f0 100%);
        }

        .sidebar {
            background: #14532d;
            border-right: 1px solid rgba(255,255,255,0.14);
            box-shadow: 10px 0 22px rgba(20, 83, 45, 0.3);
        }

        .sidebar a {
            border-radius: 999px;
            margin: 7px 0;
            padding: 10px 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(34, 197, 94, 0.22);
            border-color: rgba(255,255,255,0.18);
        }

        .navbar {
            border-radius: 14px;
            border: 1px solid #d7e5da;
            background: #ffffff;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.07);
        }

        .navbar-buttons a,
        .btn-primary,
        .card-header,
        .modal-header {
            background: #166534;
        }

        .navbar-buttons a:hover,
        .btn-primary:hover {
            background: #14532d;
        }

        h2 {
            background: none;
            -webkit-text-fill-color: #166534;
            color: #166534;
        }

        h2::after {
            background: #22c55e;
            height: 3px;
            width: 44px;
        }

        .stat-card,
        .card {
            border-radius: 14px;
            border: 1px solid #d9e6dc;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.07);
        }

        .stat-card::before {
            height: 3px;
            background: #22c55e;
        }

        .table thead th {
            background: #edf8ef;
            border-bottom: 1px solid #d6e7da;
        }

        .table tbody tr:nth-child(even) {
            background: #fafdfb;
        }

        .table tbody tr:hover {
            background: #eff8f1;
        }

        .btn-outline-secondary {
            border-radius: 999px;
        }

        .page-indicator span {
            border-radius: 999px;
            background: #edf8ef;
        }

        /* ===== Animation légumes ===== */
        .veggie-layer {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 9999;
            overflow: hidden;
            transition: opacity 0.8s ease;
        }

        .veggie {
            position: absolute;
            bottom: -40px;
            font-size: 30px;
            opacity: 0.72;
            filter: drop-shadow(0 3px 4px rgba(0, 0, 0, 0.15));
            animation: veggieFloat linear forwards;
            will-change: transform, opacity;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }

        @keyframes veggieFloat {
            0% {
                transform: translate3d(0, 0, 0) rotate(0deg) scale(0.9);
                opacity: 0;
            }
            12% {
                opacity: 0.8;
            }
            85% {
                opacity: 0.75;
            }
            100% {
                transform: translate3d(var(--drift-x, 20px), -115vh, 0) rotate(var(--spin, 180deg)) scale(1.15);
                opacity: 0;
            }
        }
    </style>
    <!-- Import des fonctions de validation -->
    <script src="assets/js/validation.js"></script>
    <!-- jsPDF for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Chart.js for graphical statistics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>🌱 ECOSAVE </h2>
        <a href="#" onclick="showSection('dashboard')" class="active">📊 Dashboard</a>
        <a href="#" onclick="showSection('recettes')">🍲 Recettes</a>
        <a href="#" onclick="showSection('facteurs')">⛽ Facteurs Émission</a>
        <a href="#" onclick="showSection('analyses')">📉 Analyses Carbone</a>
        <a href="#" onclick="showSection('stats')">📈 Statistiques</a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <!-- Navbar -->
        <div class="navbar">
            <span class="navbar-brand">🌱 ECOSAVE - Gestion Carbone</span>
            <div class="navbar-buttons">
                <span>Admin • <span id="current-time"></span></span>
                <a href="client-dashboard.php" target="_blank" title="Voir la page client">👁️ Aperçu Client</a>
            </div>
        </div>

        <!-- DASHBOARD SECTION -->
        <div id="dashboard" class="page-section active">
            <h2>📊 Dashboard</h2>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <h3 id="total-recettes">0</h3>
                        <p>Recettes</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h3 id="total-facteurs">0</h3>
                        <p>Facteurs d'Émission</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h3 id="total-analyses">0</h3>
                        <p>Analyses Carbone</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h3 id="score-moyen">0</h3>
                        <p>CO2 Moyen</p>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>📋 Dernières Recettes</h5>
                </div>
                <div class="card-body">
                    <div id="latest-recettes" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECETTES SECTION -->
        <div id="recettes" class="page-section">
            <h2>🍲 Gestion des Recettes</h2>
            
            <div class="row-mb-3">
                <input type="text" id="recette-search" class="form-control" placeholder="🔍 Rechercher une recette...">
                <button class="btn btn-primary" onclick="openRecetteModal()">➕ Ajouter recette</button>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div id="recettes-list" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>

            <div class="page-indicator">
                <button class="btn btn-sm btn-outline-secondary" id="recettes-prev" onclick="changeRecettesPage(-1)">← Précédent</button>
                <span id="recettes-page-info" style="min-width: 150px; text-align: center;"></span>
                <button class="btn btn-sm btn-outline-secondary" id="recettes-next" onclick="changeRecettesPage(1)">Suivant →</button>
            </div>
        </div>

        <!-- FACTEURS SECTION -->
        <div id="facteurs" class="page-section">
            <h2>⛽ Gestion des Facteurs d'Émission</h2>
            
            <div class="row-mb-3">
                <input type="text" id="facteur-search" class="form-control" placeholder="🔍 Rechercher un facteur...">
                <button class="btn btn-primary" onclick="openFacteurModal()">➕ Ajouter facteur</button>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div id="facteurs-list" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>

            <div class="page-indicator">
                <button class="btn btn-sm btn-outline-secondary" id="facteurs-prev" onclick="changeFacteursPage(-1)">← Précédent</button>
                <span id="facteurs-page-info" style="min-width: 150px; text-align: center;"></span>
                <button class="btn btn-sm btn-outline-secondary" id="facteurs-next" onclick="changeFacteursPage(1)">Suivant →</button>
            </div>
        </div>

        <!-- ANALYSES SECTION -->
        <div id="analyses" class="page-section">
            <h2>📉 Gestion des Analyses Carbone</h2>
            
            <div class="row-mb-3">
                <input type="text" id="analyse-search" class="form-control" placeholder="🔍 Rechercher une analyse...">
                <button class="btn btn-primary" onclick="openAnalyseModal()">➕ Nouvelle Analyse</button>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div id="analyses-list" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>

            <div class="page-indicator">
                <button class="btn btn-sm btn-outline-secondary" id="analyses-prev" onclick="changeAnalysesPage(-1)">← Précédent</button>
                <span id="analyses-page-info" style="min-width: 150px; text-align: center;"></span>
                <button class="btn btn-sm btn-outline-secondary" id="analyses-next" onclick="changeAnalysesPage(1)">Suivant →</button>
            </div>
        </div>

        <!-- STATS SECTION -->
        <div id="stats" class="page-section">
            <h2>📈 Statistiques d'Impact</h2>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 p-4">
                        <h5>Distribution des Impacts</h5>
                        <canvas id="impactChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 p-4">
                        <h5>Détails Numériques</h5>
                        <div id="danger-stats"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RECETTE MODAL -->
    <div id="recette-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="recette-modal-title">Ajouter une Recette</h5>
                <button class="btn-close" onclick="closeRecetteModal()">&times;</button>
            </div>
            <form id="recette-form">
                <div class="modal-body">
                    <input type="hidden" id="recette-id">
                    <div class="mb-3">
                        <label>Nom *</label>
                        <input type="text" id="recette-nom" name="nom" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea id="recette-description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeRecetteModal()">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="sauvegarderRecette()">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- FACTEUR MODAL -->
    <div id="facteur-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="facteur-modal-title">Ajouter un Facteur d'Émission</h5>
                <button class="btn-close" onclick="closeFacteurModal()">&times;</button>
            </div>
            <form id="facteur-form">
                <div class="modal-body">
                    <input type="hidden" id="facteur-id">
                    <div class="mb-3">
                        <label>Catégorie Aliment *</label>
                        <input type="text" id="facteur-categorie" name="categorie_aliment" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>CO2 par KG *</label>
                        <input type="text" id="facteur-co2" name="co2_par_kg" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Source Donnée *</label>
                        <input type="text" id="facteur-source" name="source_donnee" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Dernière MAJ *</label>
                        <input type="date" id="facteur-date-maj" name="date_derniere_maj" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeFacteurModal()">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="sauvegarderFacteur()">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ANALYSE MODAL -->
    <div id="analyse-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="analyse-modal-title">Nouvelle Analyse Carbone</h5>
                <button class="btn-close" onclick="closeAnalyseModal()">&times;</button>
            </div>
            <form id="analyse-form">
                <div class="modal-body">
                    <input type="hidden" id="analyse-id">
                    <div class="mb-3">
                        <label>Recette *</label>
                        <select id="analyse-recette" name="id_recette" class="form-select">
                            <option value="">Chargement...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Score CO2 Total *</label>
                        <input type="text" id="analyse-score" name="score_co2_total" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Niveau d'Impact *</label>
                        <select id="analyse-impact" name="niveau_impact" class="form-select">
                            <option value="">Sélectionner</option>
                            <option value="bas">Bas</option>
                            <option value="moyen">Moyen</option>
                            <option value="élevé">Élevé</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Méthode de Calcul *</label>
                        <input type="text" id="analyse-methode" name="methode_calcul" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Date Calcul *</label>
                        <input type="date" id="analyse-date" name="date_calcul" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAnalyseModal()">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="sauvegarderAnalyse()">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_BASE = '../../index.php';
        
        // Configuration de l'état global
        let state = {
            recettes: { page: 1, total: 1, sort: 'nom', order: 'ASC' },
            facteurs: { page: 1, total: 1, sort: 'categorie_aliment', order: 'ASC' },
            analyses: { page: 1, total: 1, sort: 'date_calcul', order: 'DESC' }
        };

        function showSection(section) {
            document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            document.getElementById(section).classList.add('active');
            
            const sidebarLinks = document.querySelectorAll('.sidebar a');
            sidebarLinks.forEach(a => {
                if(a.getAttribute('onclick')?.includes(section)) a.classList.add('active');
            });

            if (section === 'recettes') chargerRecettes();
            if (section === 'facteurs') chargerFacteurs();
            if (section === 'analyses') chargerAnalyses();
            if (section === 'dashboard') chargerDashboard();
            if (section === 'stats') chargerStats();
        }

        // --- DASHBOARD ---
        async function chargerDashboard() {
            try {
                const [rec, fact, ana] = await Promise.all([
                    fetch(`${API_BASE}?controller=Recette&action=obtenirTous&limite=1000`).then(r => r.json()).catch(e => ({success:false, message:e.message})),
                    fetch(`${API_BASE}?controller=FacteurEmission&action=obtenirTous&limite=1000`).then(r => r.json()).catch(e => ({success:false, message:e.message})),
                    fetch(`${API_BASE}?controller=AnalyseCarbone&action=obtenirTous&limite=1000`).then(r => r.json()).catch(e => ({success:false, message:e.message}))
                ]);

                if (rec.success) document.getElementById('total-recettes').textContent = rec.pagination.total;
                if (fact.success) document.getElementById('total-facteurs').textContent = fact.pagination.total;
                
                if (ana.success) {
                    document.getElementById('total-analyses').textContent = ana.pagination.total;
                    const avg = ana.analyses.length ? (ana.analyses.reduce((a, b) => a + parseFloat(b.score_co2_total), 0) / ana.analyses.length).toFixed(2) : 0;
                    document.getElementById('score-moyen').textContent = avg + ' kg';
                    
                    const latest = ana.analyses.slice(0, 5);
                    if(latest.length > 0) {
                        let html = '<table class="table"><thead><tr><th>Recette</th><th>Score</th><th>Impact</th></tr></thead><tbody>';
                        latest.forEach(a => {
                            const badge = a.niveau_impact === 'bas' ? 'success' : a.niveau_impact === 'moyen' ? 'warning' : 'danger';
                            html += `<tr><td>${a.nom_recette || 'N/A'}</td><td>${a.score_co2_total} kg</td><td><span class="badge bg-${badge}">${a.niveau_impact}</span></td></tr>`;
                        });
                        html += '</tbody></table>';
                        document.getElementById('latest-recettes').innerHTML = html;
                    } else {
                        document.getElementById('latest-recettes').innerHTML = '<p class="p-3 text-center text-muted">Aucune analyse disponible</p>';
                    }
                } else {
                    document.getElementById('latest-recettes').innerHTML = `<p class="p-3 text-danger">Erreur de chargement: ${ana.message || 'Serveur injoignable'}</p>`;
                }
            } catch (e) { 
                console.error(e);
                document.getElementById('latest-recettes').innerHTML = '<p class="p-3 text-danger">Erreur critique de connexion</p>';
            }
        }

        // --- GENERIC CHARGER ---
        async function fetchData(type, entity) {
            try {
                const { page, sort, order } = state[type];
                const resp = await fetch(`${API_BASE}?controller=${entity}&action=obtenirTous&page=${page}&limite=10&tri=${sort}&ordre=${order}`);
                if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                return await resp.json();
            } catch (e) {
                console.error(e);
                return { success: false, message: e.message };
            }
        }

        // --- RECETTES ---
        async function chargerRecettes() {
            const data = await fetchData('recettes', 'Recette');
            if (data.success) {
                state.recettes.total = data.pagination.total_pages;
                afficherRecettes(data.recettes);
                updatePagination('recettes');
            } else {
                document.getElementById('recettes-list').innerHTML = `<div class="p-4 text-danger">⚠️ Erreur: ${data.message}</div>`;
            }
        }

        function afficherRecettes(items) {
            const list = document.getElementById('recettes-list');
            if (!items.length) { list.innerHTML = '<div class="empty-state">Aucune recette</div>'; return; }
            
            let html = `<table class="table">
                <thead><tr>
                    <th class="sortable ${getSortClass('recettes', 'nom')}" onclick="handleSort('recettes', 'nom')">Nom</th>
                    <th class="sortable ${getSortClass('recettes', 'description')}" onclick="handleSort('recettes', 'description')">Description</th>
                    <th class="sortable ${getSortClass('recettes', 'date_creation')}" onclick="handleSort('recettes', 'date_creation')">Date</th>
                    <th>Actions</th>
                </tr></thead><tbody>`;
            items.forEach(r => {
                html += `<tr>
                    <td><strong>${r.nom}</strong></td>
                    <td>${r.description || ''}</td>
                    <td><small>${r.date_creation}</small></td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editerRecette(${r.id_recette})">✎</button>
                        <button class="btn btn-sm btn-warning" onclick="exportRecettePDF(${r.id_recette}, '${r.nom.replace(/'/g, "\\'")}')">📄</button>
                        <button class="btn btn-sm btn-danger" onclick="supprimerRecette(${r.id_recette})">✗</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            list.innerHTML = html;
        }

        async function sauvegarderRecette() {
            if (!validerRecette()) return;
            const id = document.getElementById('recette-id').value;
            const data = {
                nom: document.getElementById('recette-nom').value,
                description: document.getElementById('recette-description').value
            };
            const method = id ? 'PUT' : 'POST';
            const url = id ? `${API_BASE}?controller=Recette&action=mettre_a_jour&id=${id}` : `${API_BASE}?controller=Recette&action=creer`;
            
            const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
            if ((await res.json()).success) { closeRecetteModal(); chargerRecettes(); showNotification('Succès !'); }
        }

        // --- FACTEURS ---
        async function chargerFacteurs() {
            const data = await fetchData('facteurs', 'FacteurEmission');
            if (data.success) {
                state.facteurs.total = data.pagination.total_pages;
                afficherFacteurs(data.facteurs);
                updatePagination('facteurs');
            } else {
                document.getElementById('facteurs-list').innerHTML = `<div class="p-4 text-danger">⚠️ Erreur: ${data.message}</div>`;
            }
        }

        function afficherFacteurs(items) {
            const list = document.getElementById('facteurs-list');
            let html = `<table class="table">
                <thead><tr>
                    <th class="sortable ${getSortClass('facteurs', 'categorie_aliment')}" onclick="handleSort('facteurs', 'categorie_aliment')">Catégorie</th>
                    <th class="sortable ${getSortClass('facteurs', 'co2_par_kg')}" onclick="handleSort('facteurs', 'co2_par_kg')">CO2/kg</th>
                    <th class="sortable ${getSortClass('facteurs', 'source_donnee')}" onclick="handleSort('facteurs', 'source_donnee')">Source</th>
                    <th class="sortable ${getSortClass('facteurs', 'date_derniere_maj')}" onclick="handleSort('facteurs', 'date_derniere_maj')">Dernière MAJ</th>
                    <th>Actions</th>
                </tr></thead><tbody>`;
            items.forEach(f => {
                html += `<tr><td>${f.categorie_aliment}</td><td>${f.co2_par_kg}</td><td>${f.source_donnee}</td><td>${f.date_derniere_maj}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editerFacteur(${f.id_facteur})">✎</button>
                        <button class="btn btn-sm btn-warning" onclick="exportFacteurPDF(${f.id_facteur}, '${f.categorie_aliment.replace(/'/g, "\\'")}')">📄</button>
                        <button class="btn btn-sm btn-danger" onclick="supprimerFacteur(${f.id_facteur})">✗</button>
                    </td></tr>`;
            });
            list.innerHTML = html + '</tbody></table>';
        }

        async function sauvegarderFacteur() {
            if (!validerFacteur()) return;
            const id = document.getElementById('facteur-id').value;
            const data = {
                categorie_aliment: document.getElementById('facteur-categorie').value,
                co2_par_kg: document.getElementById('facteur-co2').value,
                source_donnee: document.getElementById('facteur-source').value,
                date_derniere_maj: document.getElementById('facteur-date-maj').value
            };
            const url = id ? `${API_BASE}?controller=FacteurEmission&action=mettre_a_jour&id=${id}` : `${API_BASE}?controller=FacteurEmission&action=creer`;
            const res = await fetch(url, { method: id?'PUT':'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)});
            if ((await res.json()).success) { closeFacteurModal(); chargerFacteurs(); showNotification('Succès !'); }
        }

        // --- ANALYSES (The Main Entity with Join) ---
        async function chargerAnalyses() {
            const data = await fetchData('analyses', 'AnalyseCarbone');
            if (data.success) {
                state.analyses.total = data.pagination.total_pages;
                afficherAnalyses(data.analyses);
                updatePagination('analyses');
            } else {
                document.getElementById('analyses-list').innerHTML = `<div class="p-4 text-danger">⚠️ Erreur: ${data.message}</div>`;
            }
        }

        function afficherAnalyses(items) {
            const list = document.getElementById('analyses-list');
            let html = `<table class="table">
                <thead><tr>
                    <th class="sortable ${getSortClass('analyses', 'nom_recette')}" onclick="handleSort('analyses', 'nom_recette')">Recette</th>
                    <th class="sortable ${getSortClass('analyses', 'score_co2_total')}" onclick="handleSort('analyses', 'score_co2_total')">Score CO2</th>
                    <th class="sortable ${getSortClass('analyses', 'niveau_impact')}" onclick="handleSort('analyses', 'niveau_impact')">Impact</th>
                    <th class="sortable ${getSortClass('analyses', 'methode_calcul')}" onclick="handleSort('analyses', 'methode_calcul')">Méthode</th>
                    <th class="sortable ${getSortClass('analyses', 'date_calcul')}" onclick="handleSort('analyses', 'date_calcul')">Date</th>
                    <th>Actions</th>
                </tr></thead><tbody>`;
            items.forEach(a => {
                const badge = a.niveau_impact === 'bas' ? 'success' : a.niveau_impact === 'moyen' ? 'warning' : 'danger';
                html += `<tr>
                    <td><strong>${a.nom_recette || 'N/A'}</strong></td>
                    <td>${a.score_co2_total} kg</td>
                    <td><span class="badge bg-${badge}">${a.niveau_impact}</span></td>
                    <td>${a.methode_calcul}</td>
                    <td>${a.date_calcul}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editerAnalyse(${a.id_analyse})">✎</button>
                        <button class="btn btn-sm btn-warning" onclick="exportAnalysePDF(${a.id_analyse})">📄</button>
                        <button class="btn btn-sm btn-success" style="background:#2e7d32" onclick="voirSuggestions(${a.id_analyse})" title="Suggestions Éco">💡</button>
                        <button class="btn btn-sm btn-danger" onclick="supprimerAnalyse(${a.id_analyse})">✗</button>
                    </td></tr>`;
            });
            list.innerHTML = html + '</tbody></table>';
        }

        async function sauvegarderAnalyse() {
            if (!validerAnalyse()) return;
            const id = document.getElementById('analyse-id').value;
            const data = {
                id_recette: document.getElementById('analyse-recette').value,
                score_co2_total: document.getElementById('analyse-score').value,
                niveau_impact: document.getElementById('analyse-impact').value,
                methode_calcul: document.getElementById('analyse-methode').value,
                date_calcul: document.getElementById('analyse-date').value
            };
            const url = id ? `${API_BASE}?controller=AnalyseCarbone&action=mettre_a_jour&id=${id}` : `${API_BASE}?controller=AnalyseCarbone&action=creer`;
            const res = await fetch(url, { method: id?'PUT':'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)});
            if ((await res.json()).success) { closeAnalyseModal(); chargerAnalyses(); showNotification('Analyse sauvegardée'); }
        }

        // --- PDF EXPORT ---


        // --- STATS ---
        let myChart = null;
        async function chargerStats() {
            const resp = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=obtenirTous&limite=1000`);
            const data = await resp.json();
            if (data.success) {
                const total = data.analyses.length;
                const stats = { bas: 0, moyen: 0, élevé: 0 };
                data.analyses.forEach(a => stats[a.niveau_impact]++);

                // Render Numeric Details
                let html = '<div>';
                Object.entries(stats).forEach(([lvl, count]) => {
                    const pct = total ? (count / total * 100).toFixed(0) : 0;
                    html += `
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <strong>Impact ${lvl.toUpperCase()}</strong>
                                <span>${count} (${pct}%)</span>
                            </div>
                            <div class="stat-bar-container">
                                <div class="stat-bar ${lvl}" style="width: ${pct}%"></div>
                            </div>
                        </div>
                    `;
                });
                document.getElementById('danger-stats').innerHTML = html + '</div>';

                // Render Chart.js
                const ctx = document.getElementById('impactChart').getContext('2d');
                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Bas', 'Moyen', 'Elevé'],
                        datasets: [{
                            data: [stats.bas, stats.moyen, stats.élevé],
                            backgroundColor: ['#66bb6a', '#ffa726', '#ef5350'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        cutout: '70%'
                    }
                });
            }
        }

        // --- SEARCH ---
        document.getElementById('recette-search').addEventListener('input', debounce(async (e) => {
            const term = e.target.value.trim();
            if(!validerRecherche(term)) { if(!term) chargerRecettes(); return; }
            const res = await fetch(`${API_BASE}?controller=Recette&action=rechercher&terme=${term}`);
            const data = await res.json();
            if(data.success) { afficherRecettes(data.recettes); document.getElementById('recettes-page-info').textContent = 'Résultats recherche'; }
        }, 500));

        document.getElementById('facteur-search').addEventListener('input', debounce(async (e) => {
            const term = e.target.value.trim();
            if(!term) { chargerFacteurs(); return; }
            const res = await fetch(`${API_BASE}?controller=FacteurEmission&action=rechercher&terme=${term}`);
            const data = await res.json();
            if(data.success) { afficherFacteurs(data.facteurs); document.getElementById('facteurs-page-info').textContent = 'Résultats recherche'; }
        }, 500));

        document.getElementById('analyse-search').addEventListener('input', debounce(async (e) => {
            const term = e.target.value.trim();
            if(!term) { chargerAnalyses(); return; }
            const res = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=rechercher&terme=${term}`);
            const data = await res.json();
            if(data.success) { afficherAnalyses(data.analyses); document.getElementById('analyses-page-info').textContent = 'Résultats recherche'; }
        }, 500));

        // --- UTILS ---
        function handleSort(type, col) {
            if (state[type].sort === col) {
                state[type].order = state[type].order === 'ASC' ? 'DESC' : 'ASC';
            } else {
                state[type].sort = col;
                state[type].order = 'ASC';
            }
            if (type === 'recettes') chargerRecettes();
            else if (type === 'facteurs') chargerFacteurs();
            else chargerAnalyses();
        }

        function getSortClass(type, col) {
            if (state[type].sort !== col) return '';
            return state[type].order === 'ASC' ? 'sort-asc' : 'sort-desc';
        }

        function updatePagination(type) {
            const p = state[type];
            document.getElementById(`${type}-page-info`).textContent = `Page ${p.page} / ${p.total}`;
            document.getElementById(`${type}-prev`).disabled = p.page <= 1;
            document.getElementById(`${type}-next`).disabled = p.page >= p.total;
        }

        function changeRecettesPage(d) { state.recettes.page += d; chargerRecettes(); }
        function changeFacteursPage(d) { state.facteurs.page += d; chargerFacteurs(); }
        function changeAnalysesPage(d) { state.analyses.page += d; chargerAnalyses(); }

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        function showNotification(msg) {
            const n = document.createElement('div');
            n.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#2e7d32;color:white;padding:10px 30px;border-radius:30px;z-index:9999;box-shadow:0 10px 20px rgba(0,0,0,0.2)';
            n.textContent = msg;
            document.body.appendChild(n);
            setTimeout(() => n.remove(), 2500);
        }

        // --- EXPORT PDF FUNCTIONS ---
        async function exportRecettePDF(id, nom) {
            const { jsPDF } = window.jspdf;
            const resp = await fetch(`${API_BASE}?controller=Recette&action=obtenirTous&limite=1000`);
            const data = await resp.json();
            const recette = data.recettes.find(r => r.id_recette == id);
            if (!recette) { showNotification('Erreur: Recette non trouvée'); return; }

            const doc = new jsPDF();
            const clean = (str) => (!str) ? '' : str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^\x00-\x7F]/g, "");
            
            doc.setFontSize(22);
            doc.setTextColor(46, 125, 50);
            doc.text('ECOSAVE - Fiche Recette', 20, 30);
            
            doc.setDrawColor(46, 125, 50);
            doc.line(20, 40, 190, 40);
            
            doc.setFontSize(14);
            doc.setTextColor(0, 0, 0);
            doc.text(`Nom: ${clean(recette.nom)}`, 20, 55);
            doc.text(`Date de création: ${recette.date_creation}`, 20, 70);
            
            doc.setFontSize(12);
            doc.text('Description:', 20, 85);
            const description = clean(recette.description || 'N/A');
            const lines = doc.splitTextToSize(description, 170);
            doc.text(lines, 20, 95);
            
            doc.setFontSize(10);
            doc.setTextColor(150, 150, 150);
            doc.text('Generé automatiquement par ECOSAVE Admin System', 20, 270);
            
            doc.save(`Recette_${clean(recette.nom).replace(/\s/g, '_')}.pdf`);
            showNotification('PDF Recette généré !');
        }

        async function exportFacteurPDF(id, categorie) {
            const { jsPDF } = window.jspdf;
            const resp = await fetch(`${API_BASE}?controller=FacteurEmission&action=obtenirTous&limite=1000`);
            const data = await resp.json();
            const facteur = data.facteurs.find(f => f.id_facteur == id);
            if (!facteur) { showNotification('Erreur: Facteur non trouvé'); return; }

            const doc = new jsPDF();
            const clean = (str) => (!str) ? '' : str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^\x00-\x7F]/g, "");
            
            doc.setFontSize(22);
            doc.setTextColor(46, 125, 50);
            doc.text('ECOSAVE - Facteur Emission', 20, 30);
            
            doc.setDrawColor(46, 125, 50);
            doc.line(20, 40, 190, 40);
            
            doc.setFontSize(14);
            doc.setTextColor(0, 0, 0);
            doc.text(`Catégorie: ${clean(facteur.categorie_aliment)}`, 20, 55);
            doc.text(`CO2 par KG: ${facteur.co2_par_kg} kg`, 20, 70);
            doc.text(`Source: ${clean(facteur.source_donnee || 'N/A')}`, 20, 85);
            doc.text(`Dernière MAJ: ${facteur.date_derniere_maj || 'N/A'}`, 20, 100);
            
            doc.setFontSize(11);
            doc.setTextColor(46, 125, 50);
            doc.text('Impact Environnemental:', 20, 120);
            doc.setFontSize(10);
            doc.setTextColor(0, 0, 0);
            doc.text(`Cet aliment émet ${facteur.co2_par_kg} kg de CO2 par kilogramme produit.`, 20, 130);
            
            doc.setFontSize(10);
            doc.setTextColor(150, 150, 150);
            doc.text('Generé automatiquement par ECOSAVE Admin System', 20, 270);
            
            doc.save(`Facteur_${clean(facteur.categorie_aliment).replace(/\s/g, '_')}.pdf`);
            showNotification('PDF Facteur généré !');
        }

        async function exportAnalysePDF(id) {
            const { jsPDF } = window.jspdf;
            const resp = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=obtenirTous&limite=1000`);
            const data = await resp.json();
            const analyse = data.analyses.find(a => a.id_analyse == id);
            if (!analyse) { showNotification('Erreur: Analyse non trouvée'); return; }

            const doc = new jsPDF();
            const clean = (str) => (!str) ? '' : str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^\x00-\x7F]/g, "");
            
            doc.setFontSize(22);
            doc.setTextColor(46, 125, 50);
            doc.text('ECOSAVE - Rapport Impact Carbone', 20, 30);
            
            doc.setDrawColor(46, 125, 50);
            doc.line(20, 40, 190, 40);
            
            doc.setFontSize(14);
            doc.setTextColor(0, 0, 0);
            doc.text(`Recette: ${clean(analyse.nom_recette || 'N/A')}`, 20, 55);
            doc.text(`Date du calcul: ${analyse.date_calcul}`, 20, 70);
            doc.text(`Méthode: ${clean(analyse.methode_calcul || 'N/A')}`, 20, 85);
            
            doc.setFontSize(18);
            doc.setTextColor(46, 125, 50);
            doc.text(`SCORE: ${analyse.score_co2_total} kg CO2`, 20, 105);
            
            const impactColor = analyse.niveau_impact === 'bas' ? [102, 187, 106] : 
                               analyse.niveau_impact === 'moyen' ? [255, 167, 38] : [239, 83, 80];
            doc.setTextColor(impactColor[0], impactColor[1], impactColor[2]);
            doc.setFontSize(14);
            doc.text(`Niveau: ${analyse.niveau_impact.toUpperCase()}`, 20, 125);
            
            doc.setFontSize(10);
            doc.setTextColor(150, 150, 150);
            doc.text('Generé automatiquement par ECOSAVE Admin System', 20, 270);
            
            doc.save(`Analyse_${clean(analyse.nom_recette || 'Analyse').replace(/\s/g, '_')}.pdf`);
            showNotification('PDF Analyse généré !');
        }

        // Modal Helpers
        function openRecetteModal() { document.getElementById('recette-id').value = ''; document.getElementById('recette-form').reset(); document.getElementById('recette-modal').classList.add('show'); }
        function closeRecetteModal() { document.getElementById('recette-modal').classList.remove('show'); }
        function openFacteurModal() { document.getElementById('facteur-id').value = ''; document.getElementById('facteur-form').reset(); document.getElementById('facteur-date-maj').valueAsDate = new Date(); document.getElementById('facteur-modal').classList.add('show'); }
        function closeFacteurModal() { document.getElementById('facteur-modal').classList.remove('show'); }
        async function openAnalyseModal() {
            document.getElementById('analyse-id').value = ''; document.getElementById('analyse-form').reset(); document.getElementById('analyse-date').valueAsDate = new Date();
            const r = await fetch(`${API_BASE}?controller=Recette&action=obtenirTous&limite=1000`).then(x=>x.json());
            if(r.success) document.getElementById('analyse-recette').innerHTML = '<option value="">Choisir...</option>' + r.recettes.map(x=>`<option value="${x.id_recette}">${x.nom}</option>`).join('');
            document.getElementById('analyse-modal').classList.add('show');
        }
        function closeAnalyseModal() { document.getElementById('analyse-modal').classList.remove('show'); }

        async function editerRecette(id) {
            const r = await fetch(`${API_BASE}?controller=Recette&action=obtenirTous&limite=1000`).then(x=>x.json());
            const item = r.recettes.find(x=>x.id_recette==id);
            if(item) { openRecetteModal(); document.getElementById('recette-id').value=id; document.getElementById('recette-nom').value=item.nom; document.getElementById('recette-description').value=item.description; }
        }
        async function supprimerRecette(id) { if(confirm('Supprimer ?')) { await fetch(`${API_BASE}?controller=Recette&action=supprimer&id=${id}`, {method:'DELETE'}); chargerRecettes(); } }
        
        async function editerFacteur(id) {
            const r = await fetch(`${API_BASE}?controller=FacteurEmission&action=obtenirTous&limite=1000`).then(x=>x.json());
            const item = r.facteurs.find(x=>x.id_facteur==id);
            if(item) { openFacteurModal(); document.getElementById('facteur-id').value=id; document.getElementById('facteur-categorie').value=item.categorie_aliment; document.getElementById('facteur-co2').value=item.co2_par_kg; document.getElementById('facteur-source').value=item.source_donnee; document.getElementById('facteur-date-maj').value=item.date_derniere_maj; }
        }
        async function supprimerFacteur(id) { if(confirm('Supprimer ?')) { await fetch(`${API_BASE}?controller=FacteurEmission&action=supprimer&id=${id}`, {method:'DELETE'}); chargerFacteurs(); } }

        async function editerAnalyse(id) {
            const r = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=obtenirTous&limite=1000`).then(x=>x.json());
            const item = r.analyses.find(x=>x.id_analyse==id);
            if(item) { await openAnalyseModal(); document.getElementById('analyse-id').value=id; document.getElementById('analyse-recette').value=item.id_recette; document.getElementById('analyse-score').value=item.score_co2_total; document.getElementById('analyse-impact').value=item.niveau_impact; document.getElementById('analyse-methode').value=item.methode_calcul; document.getElementById('analyse-date').value=item.date_calcul; }
        }
        async function supprimerAnalyse(id) { if(confirm('Supprimer ?')) { await fetch(`${API_BASE}?controller=AnalyseCarbone&action=supprimer&id=${id}`, {method:'DELETE'}); chargerAnalyses(); } }

        document.getElementById('current-time').textContent = new Date().toLocaleTimeString();
        setInterval(() => document.getElementById('current-time').textContent = new Date().toLocaleTimeString(), 1000);
        chargerDashboard();

        // --- CHATBOT LOGIC ---
        function toggleChat() {
            const window = document.getElementById('chatbot-window');
            window.style.display = window.style.display === 'flex' ? 'none' : 'flex';
            if (window.style.display === 'flex') {
                document.getElementById('chatbot-input').focus();
            }
        }

        async function sendChat() {
            const input = document.getElementById('chatbot-input');
            const message = input.value.trim();
            if (!message) return;

            addChatMessage(message, 'user');
            input.value = '';

            const indicator = document.getElementById('typing-indicator');
            indicator.style.display = 'block';
            scrollChat();

            try {
                const response = await fetch(`${API_BASE}?controller=Chatbot&action=chat`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message })
                });
                const data = await response.json();
                
                indicator.style.display = 'none';
                if (data.success) {
                    addChatMessage(data.response, 'ai');
                } else {
                    addChatMessage("Désolé, une erreur est survenue: " + data.message, 'ai');
                }
            } catch (e) {
                indicator.style.display = 'none';
                addChatMessage("Erreur de connexion au serveur.", 'ai');
            }
        }

        function addChatMessage(text, sender) {
            const container = document.getElementById('chatbot-messages');
            const div = document.createElement('div');
            div.className = `message ${sender}`;
            div.textContent = text;
            container.appendChild(div);
            scrollChat();
        }

        function scrollChat() {
            const container = document.getElementById('chatbot-messages');
            container.scrollTop = container.scrollHeight;
        }

        document.getElementById('chatbot-input')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendChat();
        });

        // Animation décorative de légumes (légère)
        function initVeggieAnimation() {
            const layer = document.createElement('div');
            layer.className = 'veggie-layer';
            document.body.appendChild(layer);

            const veggies = ['🥕', '🥦', '🍅', '🥬', '🫑', '🧅', '🌽'];

            const spawn = () => {
                const el = document.createElement('span');
                el.className = 'veggie';
                el.textContent = veggies[Math.floor(Math.random() * veggies.length)];
                el.style.left = `${Math.random() * 100}vw`;
                el.style.fontSize = `${26 + Math.random() * 22}px`;
                el.style.animationDuration = `${6 + Math.random() * 5}s`;
                el.style.setProperty('--drift-x', `${-40 + Math.random() * 80}px`);
                el.style.setProperty('--spin', `${-220 + Math.random() * 440}deg`);

                layer.appendChild(el);
                el.addEventListener('animationend', () => el.remove());
            };

            // Animation d'accueil uniquement: burst initial puis disparition
            for (let i = 0; i < 14; i++) {
                setTimeout(spawn, i * 160);
            }

            setTimeout(() => {
                layer.style.opacity = '0';
                setTimeout(() => layer.remove(), 900);
            }, 3600);
        }

        initVeggieAnimation();

        async function voirSuggestions(id) {
            const resp = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=obtenirTous&limite=1000`);
            const data = await resp.json();
            const a = data.analyses.find(x => x.id_analyse == id);
            if (!a) return;

            document.getElementById('suggestion-title').textContent = `Optimisation : ${a.nom_recette}`;
            document.getElementById('suggestion-content').innerHTML = '<div class="loading"><div class="spinner"></div><p>L\'IA analyse votre recette...</p></div>';
            document.getElementById('suggestion-modal').classList.add('show');

            try {
                // On récupère aussi la description de la recette
                const recResp = await fetch(`${API_BASE}?controller=Recette&action=obtenirTous&limite=1000`);
                const recData = await recResp.json();
                const recette = recData.recettes.find(x => x.id_recette == a.id_recette);

                const res = await fetch(`${API_BASE}?controller=Chatbot&action=suggererSubstitutions`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        nom: a.nom_recette, 
                        description: recette ? recette.description : '',
                        score: a.score_co2_total 
                    })
                });
                const sData = await res.json();
                
                if (sData.success) {
                    // On formate les suggestions (elles arrivent en texte Markdown-ish)
                    // Pour simplifier, on affiche le texte formaté
                    const formatted = sData.suggestions.replace(/\n/g, '<br>');
                    document.getElementById('suggestion-content').innerHTML = `
                        <div style="color:#2e7d32; margin-bottom:15px; font-weight:bold;">
                            Impact actuel : ${a.score_co2_total} kg CO2 (${a.niveau_impact})
                        </div>
                        <div class="suggestion-card">
                            ${formatted}
                        </div>
                    `;
                } else {
                    document.getElementById('suggestion-content').innerHTML = `<p class="text-danger">Erreur : ${sData.message}</p>`;
                }
            } catch (e) {
                document.getElementById('suggestion-content').innerHTML = `<p class="text-danger">Erreur de connexion</p>`;
            }
        }

        function closeSuggestionModal() { document.getElementById('suggestion-modal').classList.remove('show'); }
    </script>

    <!-- SUGGESTION MODAL -->
    <div id="suggestion-modal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h5 id="suggestion-title">Suggestions Éco-responsables</h5>
                <button class="btn-close" onclick="closeSuggestionModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="suggestion-content">
                    <!-- Contenu généré par l'IA -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="closeSuggestionModal()">Compris !</button>
            </div>
        </div>
    </div>


    <!-- CHATBOT UI -->
    <div class="chatbot-bubble" onclick="toggleChat()">
        <span>💬</span>
    </div>

    <div id="chatbot-window" class="chatbot-window">
        <div class="chatbot-header">
            <h4><span>🌱</span> ECOSAVE AI Assistant</h4>
            <button class="btn-close" onclick="toggleChat()" style="font-size: 20px;">&times;</button>
        </div>
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="message ai">Bonjour ! Je suis votre assistant ECOSAVE. Comment puis-je vous aider aujourd'hui ?</div>
        </div>
        <div id="typing-indicator" class="typing-indicator">L'assistant réfléchit...</div>
        <div class="chatbot-input-container">
            <input type="text" id="chatbot-input" placeholder="Posez votre question ici...">
            <button onclick="sendChat()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </div>
    </div>

</body>
</html>
