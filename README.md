# 📦 Locker Quote — Symfony Microservice

Lekka mikro-aplikacja oparta o **Symfony 7.1**, służąca do wyceny paczek według rozmiarów.  
Dla zadanych wymiarów (długość, szerokość, wysokość) aplikacja zwraca:
- gabaryt skrytki (`A`, `B`, `C`),
- cenę za wysyłkę,
- lub propozycję kuriera, jeśli paczka się nie mieści w paczkomacie.

---

## 🚀 Technologie

- PHP 8.3  
- Symfony 7.1 (FrameworkBundle + Twig)  
- Docker + Docker Compose  
- PHPUnit 9 (via Symfony PHPUnit Bridge)  
- (opcjonalnie) Xdebug / PCOV do raportów coverage  

---

## 🧩 Struktura

```
.
├── bin/                # narzędzia (np. phpunit)
├── config/             # konfiguracja aplikacji i routingu
├── public/             # punkt wejścia (index.php)
├── src/
│   ├── Application/LockerQuoteService.php  # logika biznesowa
│   └── Controller/
│       ├── QuoteController.php             # REST API /api/quote
│       └── FormController.php              # formularz HTML /
├── templates/          # widoki Twig
├── tests/              # testy jednostkowe i funkcjonalne
└── docker-compose.yml  # konfiguracja kontenerów
```

---

## ⚙️ Uruchomienie lokalne (Docker)

```bash
git clone https://github.com/<twoje-repo>/locker-quote.git
cd locker-quote

docker compose build
docker compose up -d
```

Aplikacja będzie dostępna pod:  
👉 [http://localhost:8000](http://localhost:8000)

---

## 🧠 API

### Endpoint: `/api/quote`

**Metoda:** `POST`  
**Content-Type:** `application/json`

#### 🔹 Przykład żądania:
```json
{
  "length": 60,
  "width": 35,
  "height": 10
}
```

#### 🔹 Przykład odpowiedzi (paczka mieści się):
```json
{
  "input": {
    "length": 60,
    "width": 35,
    "height": 10,
    "units": "cm"
  },
  "result": {
    "fits": true,
    "locker": {
      "code": "B",
      "price": 12.99,
      "inside": { "length": 64, "width": 38, "height": 19 }
    }
  }
}
```

#### 🔹 Gdy paczka się nie mieści:
```json
{
  "result": {
    "fits": false,
    "courier": {
      "name": "DPD",
      "price_estimated": 29.90,
      "reason": "Przekroczono maksymalny wymiar paczkomatu."
    }
  }
}
```

---

## 🧮 Formularz WWW

Dostępny pod:  
👉 [http://localhost:8000/](http://localhost:8000/)  
Pozwala wpisać wymiary paczki i uzyskać wynik w interfejsie HTML renderowanym w **Twig**.

---

## 🧪 Testy

Uruchamianie testów jednostkowych i funkcjonalnych:

```bash
docker compose exec php ./bin/phpunit
```

Oczekiwany wynik:

```
PHPUnit 9.6.29 by Sebastian Bergmann and contributors.

.......                                                             7 / 7 (100%)

OK (7 tests, 20 assertions)
```

---

## 🧾 Pokrycie kodu (coverage)

### 🔹 Wymagane:
Zainstaluj **Xdebug** lub **PCOV** w kontenerze PHP.  
Przykład (Dockerfile):
```dockerfile
RUN pecl install xdebug && docker-php-ext-enable xdebug
```

### 🔹 Generowanie raportu:
```bash
docker compose exec -e XDEBUG_MODE=coverage php ./bin/phpunit --coverage-html var/coverage
```

Otwórz w przeglądarce:
```
var/coverage/index.html
```

---

## 🧰 Przydatne komendy

| Komenda | Działanie |
|----------|-----------|
| `docker compose up -d` | uruchamia aplikację |
| `docker compose exec php bash` | wchodzi do kontenera PHP |
| `docker compose exec php ./bin/phpunit` | uruchamia testy |
| `docker compose exec php ./bin/phpunit --coverage-html var/coverage` | generuje raport coverage |
| `docker compose down -v` | usuwa kontenery i wolumeny |

---

## 🧱 Autor

**JJ**  
💼 PHP / Symfony Developer

---

> 📘 Projekt stworzony jako demonstracja mikroserwisu Symfony z kompletną konfiguracją testów i CI/CD gotową do rozbudowy.
