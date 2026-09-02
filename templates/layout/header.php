<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Notation Universitaire</title>
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-hover: #1d4ed8;
            --success: #15803d;
            --danger: #b91c1c;
            --warning: #b45309;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #0f172a;
            --border: #e2e8f0;
        }
        html * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--bg); color: var(--text); line-height: 1.5; }
        header { background-color: var(--primary); color: #ffffff; padding: 1rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .nav-container { max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .nav-brand { font-size: 1.25rem; font-weight: bold; color: #ffffff; text-decoration: none; }
        .nav-links { display: flex; gap: 1rem; }
        .nav-link { color: #ffffff; text-decoration: none; font-weight: 500; padding: 0.5rem 0.75rem; border-radius: 4px; transition: background-color 0.2s; }
        .nav-link:hover { background-color: rgba(255, 255, 255, 0.15); }
        main { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: var(--card-bg); border-radius: 8px; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 1.5rem; margin-bottom: 1.5rem; }
        h1 { font-size: 1.75rem; margin-bottom: 1rem; color: var(--primary); }
        h2 { font-size: 1.25rem; margin-bottom: 0.75rem; color: var(--text); }
        .btn { display: inline-block; background-color: var(--primary); color: #ffffff; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-weight: 500; border: none; cursor: pointer; transition: background-color 0.2s; }
        .btn:hover { background-color: var(--primary-hover); }
        .btn-secondary { background-color: #64748b; }
        .btn-secondary:hover { background-color: #475569; }
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .badge-success { background-color: #dcfce7; color: var(--success); }
        .badge-danger { background-color: #fee2e2; color: var(--danger); }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); }
        th { background-color: #f1f5f9; font-weight: 600; color: #475569; }
        tr:hover { background-color: #f8fafc; }
        .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; }
        .alert-danger { background-color: #fef2f2; color: var(--danger); border: 1px solid #fecaca; }
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--text); }
        input[type="text"], input[type="number"], input[type="datetime-local"] { width: 100%; padding: 0.625rem; border: 1px solid var(--border); border-radius: 4px; font-size: 1rem; }
        input:focus { outline: none; border-color: var(--primary); ring: 2px rgba(30, 58, 138, 0.2); }
        .details-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .detail-item { padding: 0.75rem; background: #f8fafc; border-radius: 6px; border: 1px solid var(--border); }
        .detail-label { font-size: 0.85rem; color: #64748b; text-transform: uppercase; font-weight: 600; }
        .detail-value { font-size: 1.25rem; font-weight: 700; margin-top: 0.25rem; }
    </style>
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="/copies" class="nav-brand">Notation Universitaire</a>
            <nav class="nav-links">
                <a href="/copies" class="nav-link">Toutes les copies</a>
                <a href="/copies/create" class="nav-link">Soumettre une copie</a>
            </nav>
        </div>
    </header>
    <main>
