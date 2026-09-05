#!/usr/bin/env python3
"""Static sanity checks for the Espelunca Blog block theme."""

from __future__ import annotations

import json
import pathlib
import sys

ROOT = pathlib.Path(__file__).resolve().parents[1]
REQUIRED = [
    "style.css",
    "theme.json",
    "functions.php",
    "templates/index.html",
    "templates/home.html",
    "templates/single.html",
    "templates/page.html",
    "parts/header.html",
    "parts/footer.html",
]

errors: list[str] = []

for relative in REQUIRED:
    if not (ROOT / relative).is_file():
        errors.append(f"arquivo obrigatório ausente: {relative}")

for path in [ROOT / "theme.json", *sorted((ROOT / "styles").glob("*.json"))]:
    try:
        json.loads(path.read_text(encoding="utf-8"))
    except Exception as exc:  # noqa: BLE001
        errors.append(f"JSON inválido em {path.relative_to(ROOT)}: {exc}")

for path in [*sorted((ROOT / "templates").glob("*.html")), *sorted((ROOT / "parts").glob("*.html")), *sorted((ROOT / "patterns").glob("*.php"))]:
    text = path.read_text(encoding="utf-8")
    for line_number, line in enumerate(text.splitlines(), start=1):
        if "<!-- wp:" not in line:
            continue
        start = line.find("{")
        end = line.rfind("}")
        if start == -1 and end == -1:
            continue
        if start == -1 or end < start:
            errors.append(f"atributos de bloco malformados em {path.relative_to(ROOT)}:{line_number}")
            continue
        raw = line[start : end + 1]
        try:
            json.loads(raw)
        except json.JSONDecodeError as exc:
            errors.append(f"JSON de bloco inválido em {path.relative_to(ROOT)}:{line_number}: {exc}")

style = (ROOT / "style.css").read_text(encoding="utf-8") if (ROOT / "style.css").exists() else ""
for header in ("Theme Name:", "Version:", "Text Domain:"):
    if header not in style:
        errors.append(f"cabeçalho obrigatório ausente em style.css: {header}")

if errors:
    print("Falha na validação do tema:", file=sys.stderr)
    for error in errors:
        print(f"- {error}", file=sys.stderr)
    raise SystemExit(1)

print("Espelunca Blog: validação estática concluída com sucesso.")
