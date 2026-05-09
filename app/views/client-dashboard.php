<?php
/**
 * Dashboard Client - Consultation des recettes et analyses carbone
 * Style ECOSAVE (Vert écologique)
 * Point d'accès: http://localhost/gestion-allergies/app/views/client-dashboard.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOSAVE - Mon Espace Carbone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f9f4;
        }
        
        header {
            background-color: #2e7d32;
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        header a {
            color: white;
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        header a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        h1 {
            color: #2e7d32;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        
        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .tab-btn {
            background: none;
            border: none;
            padding: 12px 20px;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            color: #666;
            transition: all 0.3s;
        }
        
        .tab-btn:hover, .tab-btn.active {
            color: #2e7d32;
            border-bottom-color: #2e7d32;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .info-box {
            background: #e8f5e9;
            border-left: 4px solid #2e7d32;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #1b5e20;
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .search-box input {
            width: 100%;
            max-width: 400px;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            border-color: #2e7d32;
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }
        
        .items-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .item-card {
            background: white;
            border-left: 4px solid #66bb6a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .item-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .item-card h5 {
            color: #2e7d32;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .item-card p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }
        
        .badge.impact-bas { background: #c8e6c9; color: #2e7d32; }
        .badge.impact-moyen { background: #ffe0b2; color: #e65100; }
        .badge.impact-élevé { background: #ffcdd2; color: #c62828; }
        
        .loading { text-align: center; padding: 40px; }
        .spinner {
            width: 40px; height: 40px;
            border: 3px solid #e0e0e0;
            border-top: 3px solid #2e7d32;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        .empty-state { text-align: center; padding: 60px 20px; color: #999; }
        
        .pagination {
            display: flex; justify-content: center; gap: 10px; margin-top: 30px;
        }
        
        .btn {
            padding: 8px 12px; border: 1px solid #ddd; background: white;
            color: #666; border-radius: 5px; cursor: pointer; transition: all 0.3s;
        }
        
        .btn:hover:not(:disabled) { background: #f4f9f4; border-color: #2e7d32; color: #2e7d32; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        
        footer {
            background-color: #2e7d32; color: white; text-align: center;
            padding: 20px; margin-top: 60px;
        }

        /* ===== UI REFRESH (sans modification HTML) ===== */
        :root {
            --brand-1: #4f46e5;
            --brand-2: #7c3aed;
            --ink-900: #0f172a;
            --ink-700: #334155;
            --line: #e2e8f0;
            --surface: #ffffff;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink-900);
            background:
                radial-gradient(circle at 85% 10%, rgba(124, 58, 237, 0.12), transparent 35%),
                linear-gradient(155deg, #f4f7ff 0%, #eef2ff 100%);
        }

        header {
            background: linear-gradient(135deg, #1e3a8a, #4f46e5);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
        }

        h1 {
            color: #1e3a8a;
        }

        .subtitle {
            color: #475569;
        }

        .tabs {
            border-bottom-color: #dbe3ff;
        }

        .tab-btn:hover,
        .tab-btn.active {
            color: #4338ca;
            border-bottom-color: #4338ca;
        }

        .info-box {
            background: #eef2ff;
            border-left-color: #4f46e5;
            color: #3730a3;
            border-radius: 10px;
        }

        .search-box input {
            border-color: #cbd5e1;
            border-radius: 10px;
        }

        .search-box input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.2);
        }

        .item-card {
            border: 1px solid var(--line);
            border-left: 4px solid #6366f1;
            border-radius: 12px;
            background: var(--surface);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        }

        .item-card h5 {
            color: #1f2937;
        }

        .item-card p {
            color: var(--ink-700);
        }

        .badge.impact-bas { background: #dcfce7; color: #15803d; }
        .badge.impact-moyen { background: #fef3c7; color: #b45309; }
        .badge.impact-élevé { background: #fee2e2; color: #b91c1c; }

        .btn {
            border-radius: 10px;
            border-color: #cbd5e1;
        }

        .btn:hover:not(:disabled) {
            background: #eef2ff;
            border-color: #818cf8;
            color: #4338ca;
        }

        footer {
            background: linear-gradient(135deg, #0f172a, #1e293b);
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

        header {
            background: linear-gradient(135deg, var(--green-800), var(--green-700));
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
        }

        h1 {
            color: var(--green-800);
        }

        .subtitle,
        .item-card p {
            color: var(--text-700);
        }

        .tabs {
            border-bottom-color: #d8e8d9;
        }

        .tab-btn:hover,
        .tab-btn.active {
            color: var(--green-800);
            border-bottom-color: var(--green-800);
        }

        .info-box {
            background: var(--green-100);
            border-left-color: var(--green-700);
            color: var(--green-900);
            border-radius: 8px;
        }

        .search-box input {
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .search-box input:focus {
            border-color: var(--green-500);
            box-shadow: 0 0 0 3px rgba(102, 187, 106, 0.22);
        }

        .item-card {
            border: 1px solid var(--line-soft);
            border-left: 4px solid var(--green-500);
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .item-card:hover {
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }

        .item-card h5 {
            color: var(--green-800);
        }

        .badge.impact-bas { background: #c8e6c9; color: #2e7d32; }
        .badge.impact-moyen { background: #ffe0b2; color: #e65100; }
        .badge.impact-élevé { background: #ffcdd2; color: #c62828; }

        .btn {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            box-shadow: none;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .btn:hover:not(:disabled) {
            background: #edf7ed;
            border-color: var(--green-500);
            color: var(--green-800);
        }

        footer {
            background: linear-gradient(135deg, var(--green-900), var(--green-800));
        }

        /* ===== ULTRA REDESIGN ===== */
        :root {
            --green-900: #14532d;
            --green-800: #166534;
            --green-700: #15803d;
            --green-500: #22c55e;
            --green-100: #eaf8ed;
            --panel: rgba(255,255,255,0.95);
            --text-900: #0f172a;
            --text-700: #334155;
            --text-500: #64748b;
            --line: #e3e8e6;
        }

        body {
            font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at 6% 8%, rgba(34, 197, 94, 0.14), transparent 28%),
                radial-gradient(circle at 95% 0%, rgba(74, 222, 128, 0.11), transparent 24%),
                linear-gradient(145deg, #f7fbf7 0%, #edf7ee 100%);
            color: var(--text-900);
        }

        header {
            background: linear-gradient(135deg, #14532d 0%, #166534 70%, #15803d 100%);
            box-shadow: 0 10px 24px rgba(20, 83, 45, 0.28);
            padding: 16px 34px;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .logo {
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        header a {
            border-radius: 10px;
            font-weight: 700;
            border: 1px solid rgba(255,255,255,0.22);
        }

        .container {
            max-width: 1240px;
            padding: 34px 20px;
        }

        h1 {
            color: var(--green-800);
            font-size: 2.05rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .subtitle {
            color: var(--text-500);
            font-size: 1.02rem;
            margin-bottom: 26px;
        }

        .tabs {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 6px;
            width: fit-content;
            gap: 6px;
            margin-bottom: 18px;
            box-shadow: 0 8px 20px rgba(15,23,42,0.06);
        }

        .tab-btn {
            border-radius: 9px;
            padding: 10px 14px;
            font-weight: 700;
            color: #48614f;
            border-bottom: none;
        }

        .tab-btn:hover,
        .tab-btn.active {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: #fff;
        }

        .info-box {
            border-radius: 12px;
            border-left: none;
            border: 1px solid #d2e8d8;
            background: linear-gradient(135deg, #eefaf1, #e8f7eb);
            color: #1f5131;
            padding: 14px 16px;
            font-weight: 600;
        }

        .search-box input {
            max-width: 480px;
            border-radius: 10px;
            border: 1px solid #d2ded5;
            background: rgba(255,255,255,0.96);
        }

        .search-box input:focus {
            border-color: var(--green-500);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.2);
        }

        .items-list {
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
        }

        .item-card {
            border: 1px solid var(--line);
            border-left: 4px solid var(--green-500);
            border-radius: 14px;
            padding: 18px;
            background: var(--panel);
            box-shadow: 0 8px 18px rgba(15,23,42,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .item-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px rgba(15,23,42,0.11);
        }

        .item-card h5 {
            color: var(--green-800);
            font-size: 1.08rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .item-card p {
            color: var(--text-700);
            margin: 6px 0;
            line-height: 1.45;
        }

        .badge {
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 6px 10px;
            letter-spacing: 0.2px;
        }

        .badge.impact-bas { background: #dcfce7; color: #166534; }
        .badge.impact-moyen { background: #fef3c7; color: #b45309; }
        .badge.impact-élevé { background: #fee2e2; color: #b91c1c; }

        .pagination {
            margin-top: 24px;
            gap: 8px;
        }

        .btn {
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-weight: 700;
            padding: 9px 12px;
            background: #fff;
        }

        .btn:hover:not(:disabled) {
            background: #edf8ef;
            border-color: #86efac;
            color: #166534;
        }

        footer {
            margin-top: 46px;
            padding: 22px;
            background: linear-gradient(135deg, #14532d, #166534);
            box-shadow: 0 -8px 18px rgba(20, 83, 45, 0.2);
        }

        @media (max-width: 768px) {
            header {
                padding: 14px 16px;
                flex-direction: column;
                gap: 12px;
            }
            h1 {
                font-size: 1.6rem;
            }
            .tabs {
                width: 100%;
            }
            .tab-btn {
                flex: 1;
            }
        }

        /* ===== PREMIUM POLISH V2 ===== */
        body {
            background:
                radial-gradient(circle at 8% 10%, rgba(34, 197, 94, 0.11), transparent 30%),
                radial-gradient(circle at 92% 0%, rgba(16, 185, 129, 0.09), transparent 26%),
                linear-gradient(135deg, #f7fcf8 0%, #edf7ef 100%);
        }

        .container {
            position: relative;
        }

        h1 {
            background: linear-gradient(135deg, #14532d, #16a34a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.2rem;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 1.06rem;
        }

        .info-box {
            box-shadow: 0 6px 14px rgba(21, 128, 61, 0.08);
        }

        .search-box input {
            max-width: 540px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        }

        .items-list {
            gap: 18px;
        }

        .item-card {
            position: relative;
            overflow: hidden;
            border: 1px solid #d8e6dc;
            border-left-width: 5px;
            min-height: 150px;
        }

        .item-card::after {
            content: "";
            position: absolute;
            right: -24px;
            top: -24px;
            width: 78px;
            height: 78px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.12), rgba(34, 197, 94, 0));
            pointer-events: none;
        }

        .item-card h5 {
            font-size: 1.22rem;
            margin-bottom: 10px;
        }

        .item-card p small {
            color: #64748b;
            font-weight: 500;
        }

        .pagination {
            margin-top: 28px;
        }

        .btn {
            min-width: 108px;
        }

        .btn:disabled {
            opacity: 0.45;
        }

        footer {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.75rem;
            }
            .item-card {
                min-height: auto;
            }
        }

        /* ===== THEME GESTION CARBONE ===== */
        body {
            background:
                radial-gradient(circle at 12% 8%, rgba(34, 197, 94, 0.16), transparent 26%),
                radial-gradient(circle at 88% 0%, rgba(20, 83, 45, 0.14), transparent 24%),
                linear-gradient(135deg, #f3f7f4 0%, #e8f1ea 45%, #e2ece5 100%);
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: 0.28;
            background-image:
                linear-gradient(90deg, rgba(20, 83, 45, 0.06) 1px, transparent 1px),
                linear-gradient(rgba(20, 83, 45, 0.06) 1px, transparent 1px);
            background-size: 34px 34px;
            mask-image: radial-gradient(circle at center, black 38%, transparent 100%);
        }

        header,
        .container,
        footer {
            position: relative;
            z-index: 1;
        }

        .item-card {
            background:
                linear-gradient(180deg, rgba(255,255,255,0.96), rgba(250, 255, 251, 0.92));
            border-left-color: #16a34a;
        }

        .item-card::after {
            background: radial-gradient(circle, rgba(22, 163, 74, 0.16), rgba(22, 163, 74, 0));
        }

        .badge.impact-bas { background: #dcfce7; color: #166534; }
        .badge.impact-moyen { background: #fef3c7; color: #92400e; }
        .badge.impact-élevé { background: #fee2e2; color: #991b1b; }

        footer {
            background: linear-gradient(135deg, #0f3d24, #14532d);
        }

        /* ===== STYLE ALTERNATIF (VERT CONSERVÉ) ===== */
        body {
            background:
                linear-gradient(180deg, #f6fbf7 0%, #eef6f0 100%);
        }

        header {
            background: #166534;
            box-shadow: 0 6px 18px rgba(22, 101, 52, 0.22);
            border-bottom: 1px solid rgba(255,255,255,0.14);
        }

        .tabs {
            background: #ffffff;
            border: 1px solid #d8e7db;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        }

        .tab-btn {
            color: #2f4a36;
            border-radius: 999px;
            padding: 10px 16px;
        }

        .tab-btn:hover,
        .tab-btn.active {
            background: #22c55e;
            color: #ffffff;
        }

        .info-box {
            background: #edf9f0;
            border: 1px solid #cfe8d5;
            color: #14532d;
            box-shadow: none;
        }

        .search-box input {
            border: 1px solid #c9dbce;
            border-radius: 999px;
            padding-left: 18px;
            box-shadow: none;
        }

        .search-box input:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }

        .item-card {
            border: 1px solid #d7e5da;
            border-left: 0;
            border-radius: 16px;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.07);
        }

        .item-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            border-radius: 16px 0 0 16px;
            background: linear-gradient(180deg, #22c55e, #16a34a);
        }

        .item-card::after {
            display: none;
        }

        .item-card h5 {
            color: #166534;
        }

        .pagination .btn {
            border-radius: 999px;
            min-width: 120px;
        }

        footer {
            background: #14532d;
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
            animation: veggieFloat linear forwards;
            will-change: transform, opacity;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.22);
        }

        @keyframes veggieFloat {
            0% {
                transform: translate3d(0, 0, 0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.78;
            }
            100% {
                transform: translate3d(var(--drift-x, 20px), -115vh, 0) rotate(var(--spin, 180deg));
                opacity: 0;
            }
        }

        /* ===== ORGANIC STYLE INSPIRED ===== */
        body {
            background:
                radial-gradient(circle at 12% 8%, rgba(186, 221, 184, 0.42), transparent 35%),
                linear-gradient(145deg, #6ea564 0%, #7cad72 16%, #eff6eb 17%, #eff6eb 100%);
            min-height: 100vh;
            color: #1f3a26;
        }

        header {
            background: linear-gradient(90deg, #1f6a3a, #2f7d49);
            border-bottom: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 10px 22px rgba(20, 83, 45, 0.28);
        }

        .logo {
            font-size: 30px;
            font-weight: 900;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        header nav span {
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        header a {
            border-radius: 8px;
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .container {
            background: #f3f8ee;
            border: 1px solid #d8e8d2;
            border-radius: 0;
            margin-top: 0;
            box-shadow: 0 26px 42px rgba(18, 48, 28, 0.24);
            padding: 34px 26px 40px;
        }

        h1 {
            color: #143924;
            -webkit-text-fill-color: #143924;
            background: none;
            font-size: 3rem;
            line-height: 1.04;
            font-weight: 900;
            max-width: 640px;
            margin-bottom: 12px;
        }

        .subtitle {
            color: #49624f;
            font-size: 1.12rem;
            max-width: 670px;
            margin-bottom: 26px;
        }

        .tabs {
            width: fit-content;
            border-radius: 0;
            background: #e7f0e2;
            border: 1px solid #cfe0c8;
            box-shadow: none;
            margin-bottom: 20px;
        }

        .tab-btn {
            border-radius: 0;
            font-weight: 800;
            color: #2b4f35;
            padding: 12px 18px;
        }

        .tab-btn:hover,
        .tab-btn.active {
            background: #2f7d49;
            color: #ffffff;
        }

        .info-box {
            border-radius: 0;
            border: 1px solid #cfe0c8;
            background: #e8f3e4;
            color: #295a35;
            box-shadow: none;
            font-weight: 700;
        }

        .search-box input {
            border-radius: 0;
            border: 1px solid #c8d9c1;
            background: #ffffff;
            max-width: 520px;
        }

        .search-box input:focus {
            border-color: #3e8e59;
            box-shadow: 0 0 0 3px rgba(62, 142, 89, 0.16);
        }

        .items-list {
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 18px;
        }

        .item-card {
            border: 1px solid #d2e2cc;
            border-radius: 0;
            background: #ffffff;
            box-shadow: 0 6px 14px rgba(31, 58, 38, 0.08);
            padding: 18px 16px;
            min-height: 180px;
        }

        .item-card::before,
        .item-card::after {
            display: none;
        }

        .item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(31, 58, 38, 0.12);
        }

        .item-card h5 {
            color: #1f4d2d;
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1.05;
            text-transform: capitalize;
            margin-bottom: 8px;
        }

        .item-card p {
            color: #4b5f50;
            font-size: 1rem;
        }

        .item-card p small {
            color: #617664;
        }

        .badge {
            border-radius: 999px;
            font-weight: 800;
            padding: 6px 12px;
            font-size: 0.82rem;
        }

        .pagination {
            margin-top: 24px;
            justify-content: flex-start;
        }

        .pagination .btn {
            border-radius: 0;
            min-width: 124px;
            background: #ffffff;
            border: 1px solid #cbdcc6;
            color: #1f4d2d;
            font-weight: 800;
        }

        .pagination .btn:hover:not(:disabled) {
            background: #e8f3e4;
            border-color: #3e8e59;
            color: #1a4026;
        }

        footer {
            margin-top: 0;
            background: linear-gradient(180deg, #245c35, #1f4d2d);
            box-shadow: 0 -6px 18px rgba(20, 51, 29, 0.25);
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        @media (max-width: 768px) {
            .container {
                padding: 22px 14px 30px;
            }

            h1 {
                font-size: 2.1rem;
            }

            .items-list {
                grid-template-columns: 1fr;
            }

            .pagination {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">🌱 ECOSAVE</div>
        <nav>
            <span style="margin-right: 20px;">Portail Public</span>
            <a href="admin.php" target="_blank">⚙️ Administration</a>
        </nav>
    </header>

    <div class="container">
        <h1>🌍 Mon Espace Carbone</h1>
        <p class="subtitle">Consultez les recettes et l'impact de votre alimentation sur la planète</p>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab(event, 'recettes')">🥗 Recettes</button>
            <button class="tab-btn" onclick="switchTab(event, 'analyses')">📊 Analyses Carbone</button>
        </div>

        <!-- RECETTES TAB -->
        <div id="recettes" class="tab-content active">
            <div class="info-box">ℹ️ Explorez notre catalogue de recettes et découvrez leur composition.</div>
            <div class="search-box">
                <input type="text" id="recette-search" placeholder="🔍 Rechercher une recette...">
            </div>
            <div id="recettes-list" class="items-list">
                <div class="loading"><div class="spinner"></div></div>
            </div>
            <div class="pagination">
                <button class="btn" id="recettes-prev" onclick="changePage('recettes', -1)">← Précédent</button>
                <span id="recettes-page-info" style="min-width: 120px; text-align: center; line-height: 34px;"></span>
                <button class="btn" id="recettes-next" onclick="changePage('recettes', 1)">Suivant →</button>
            </div>
        </div>

        <!-- ANALYSES TAB -->
        <div id="analyses" class="tab-content">
            <div class="info-box">ℹ️ Consultez les scores de CO2 calculés pour nos recettes phares.</div>
            <div class="search-box">
                <input type="text" id="analyse-search" placeholder="🔍 Rechercher une analyse...">
            </div>
            <div id="analyses-list" class="items-list">
                <div class="loading"><div class="spinner"></div></div>
            </div>
            <div class="pagination">
                <button class="btn" id="analyses-prev" onclick="changePage('analyses', -1)">← Précédent</button>
                <span id="analyses-page-info" style="min-width: 120px; text-align: center; line-height: 34px;"></span>
                <button class="btn" id="analyses-next" onclick="changePage('analyses', 1)">Suivant →</button>
            </div>
        </div>
    </div>

    <footer>© 2026 ECOSAVE - Plateforme de Gestion de l'Empreinte Carbone</footer>

    <script>
        const API_BASE = '../../index.php';
        let state = {
            recettes: { page: 1, total: 1 },
            analyses: { page: 1, total: 1 }
        };

        function switchTab(event, tabName) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
            
            if (tabName === 'recettes') chargerRecettes();
            if (tabName === 'analyses') chargerAnalyses();
        }

        async function chargerRecettes() {
            try {
                const resp = await fetch(`${API_BASE}?controller=Recette&action=obtenirTous&page=${state.recettes.page}&limite=6`);
                if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                const data = await resp.json();
                if (data.success) {
                    state.recettes.total = data.pagination.total_pages;
                    afficherRecettes(data.recettes);
                    updatePagination('recettes', data.pagination);
                }
            } catch (e) {
                console.error(e);
                document.getElementById('recettes-list').innerHTML = `<div class="empty-state"><p class="text-danger">Erreur de connexion au serveur</p></div>`;
            }
        }

        function afficherRecettes(items) {
            const list = document.getElementById('recettes-list');
            if (!items.length) { list.innerHTML = '<div class="empty-state"><h3>Aucune recette trouvée</h3></div>'; return; }
            
            list.innerHTML = items.map(r => `
                <div class="item-card">
                    <h5>${r.nom}</h5>
                    <p>${r.description || 'Pas de description'}</p>
                    <p><small>Créée le ${r.date_creation}</small></p>
                    <div id="ana-${r.id_recette}" style="margin-top:10px"></div>
                </div>
            `).join('');

            // Fetch brief analysis for each
            items.forEach(async r => {
                const resp = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=obtenirTous&page=1&limite=1000`);
                const d = await resp.json();
                const a = d.analyses.find(x => x.id_recette == r.id_recette);
                if(a) {
                    document.getElementById(`ana-${r.id_recette}`).innerHTML = `
                        <span class="badge impact-${a.niveau_impact.toLowerCase()}">Score: ${a.score_co2_total} kg CO2</span>
                    `;
                }
            });
        }

        async function chargerAnalyses() {
            try {
                const resp = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=obtenirTous&page=${state.analyses.page}&limite=6`);
                if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                const data = await resp.json();
                if (data.success) {
                    state.analyses.total = data.pagination.total_pages;
                    afficherAnalyses(data.analyses);
                    updatePagination('analyses', data.pagination);
                }
            } catch (e) {
                console.error(e);
                document.getElementById('analyses-list').innerHTML = `<div class="empty-state"><p class="text-danger">Erreur de connexion au serveur</p></div>`;
            }
        }

        function afficherAnalyses(items) {
            const list = document.getElementById('analyses-list');
            if (!items.length) { list.innerHTML = '<div class="empty-state"><h3>Aucune analyse disponible</h3></div>'; return; }
            
            list.innerHTML = items.map(a => {
                const badgeClass = a.niveau_impact === 'bas' ? 'impact-bas' : a.niveau_impact === 'moyen' ? 'impact-moyen' : 'impact-élevé';
                return `
                <div class="item-card" style="border-left-color: ${a.niveau_impact === 'bas' ? '#66bb6a' : a.niveau_impact === 'moyen' ? '#ffa726' : '#ef5350'}">
                    <h5>Analyse: ${a.nom_recette || 'Recette'}</h5>
                    <div style="margin-bottom: 10px;">
                        <span class="badge ${badgeClass}">${a.niveau_impact.toUpperCase()} IMPACT</span>
                    </div>
                    <p><strong>Score CO2:</strong> <span style="font-size: 1.2rem; color: #2e7d32; font-weight: bold;">${a.score_co2_total}</span> kg</p>
                    <p><strong>Méthode:</strong> ${a.methode_calcul}</p>
                    <p><small>Calculée le ${a.date_calcul}</small></p>
                </div>
            `}).join('');

        }

        function updatePagination(type, pagin) {
            document.getElementById(`${type}-page-info`).textContent = `Page ${pagin.page} / ${pagin.total_pages}`;
            document.getElementById(`${type}-prev`).disabled = pagin.page === 1;
            document.getElementById(`${type}-next`).disabled = pagin.page === pagin.total_pages;
        }

        function changePage(type, dir) {
            state[type].page += dir;
            if (type === 'recettes') chargerRecettes(); else chargerAnalyses();
        }

        // Search
        document.getElementById('recette-search').addEventListener('input', async (e) => {
            const term = e.target.value.trim();
            if (!term) { state.recettes.page = 1; chargerRecettes(); return; }
            const resp = await fetch(`${API_BASE}?controller=Recette&action=rechercher&terme=${encodeURIComponent(term)}`);
            const data = await resp.json();
            if (data.success) {
                afficherRecettes(data.recettes);
                document.getElementById('recettes-page-info').textContent = `${data.count} résultat(s)`;
                document.getElementById('recettes-prev').disabled = true;
                document.getElementById('recettes-next').disabled = true;
            }
        });

        document.getElementById('analyse-search').addEventListener('input', async (e) => {
            const term = e.target.value.trim();
            if (!term) { state.analyses.page = 1; chargerAnalyses(); return; }
            const resp = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=rechercher&terme=${encodeURIComponent(term)}`);
            const data = await resp.json();
            if (data.success) {
                afficherAnalyses(data.analyses);
                document.getElementById('analyses-page-info').textContent = `${data.analyses.length} résultat(s)`;
                document.getElementById('analyses-prev').disabled = true;
                document.getElementById('analyses-next').disabled = true;
            }
        });

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
                el.style.setProperty('--drift-x', `${-36 + Math.random() * 72}px`);
                el.style.setProperty('--spin', `${-210 + Math.random() * 420}deg`);
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

        chargerRecettes();
    </script>
</body>
</html>
