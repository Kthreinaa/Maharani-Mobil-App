from __future__ import annotations

from pathlib import Path
from typing import Iterable

from PIL import Image, ImageDraw, ImageFont


ROOT = Path(__file__).resolve().parents[1]
OUTPUT_DIR = ROOT / "docs" / "analysis" / "diagrams"
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

BG = "#F6F8FC"
NAVY = "#11244D"
BLUE = "#DCEAFE"
MID_BLUE = "#9BB6E3"
TEXT = "#20304A"
MUTED = "#5F6E86"
ACCENT = "#F5A623"
GREEN = "#DDF6E8"
ROSE = "#FCE4E4"
WHITE = "#FFFFFF"
BORDER = "#C8D5EA"


def load_font(size: int, bold: bool = False) -> ImageFont.FreeTypeFont | ImageFont.ImageFont:
    candidates = [
        ("C:/Windows/Fonts/arialbd.ttf" if bold else "C:/Windows/Fonts/arial.ttf"),
        ("C:/Windows/Fonts/segoeuib.ttf" if bold else "C:/Windows/Fonts/segoeui.ttf"),
    ]
    for candidate in candidates:
        path = Path(candidate)
        if path.exists():
            return ImageFont.truetype(str(path), size=size)
    return ImageFont.load_default()


FONT_TITLE = load_font(34, bold=True)
FONT_SUBTITLE = load_font(22, bold=True)
FONT_BODY = load_font(17, bold=False)
FONT_SMALL = load_font(14, bold=False)
FONT_SMALL_BOLD = load_font(14, bold=True)
FONT_ACTOR = load_font(18, bold=True)


def wrap_text(draw: ImageDraw.ImageDraw, text: str, font: ImageFont.ImageFont, max_width: int) -> list[str]:
    words = text.split()
    lines: list[str] = []
    current = ""
    for word in words:
        trial = word if not current else f"{current} {word}"
        width = draw.textbbox((0, 0), trial, font=font)[2]
        if width <= max_width:
            current = trial
        else:
            if current:
                lines.append(current)
            current = word
    if current:
        lines.append(current)
    return lines or [text]


def draw_multiline(
    draw: ImageDraw.ImageDraw,
    xy: tuple[int, int],
    text: str,
    font: ImageFont.ImageFont,
    fill: str,
    max_width: int,
    line_gap: int = 6,
) -> int:
    x, y = xy
    lines = wrap_text(draw, text, font, max_width)
    line_height = draw.textbbox((0, 0), "Ag", font=font)[3]
    for line in lines:
        draw.text((x, y), line, font=font, fill=fill)
        y += line_height + line_gap
    return y


def draw_box(
    draw: ImageDraw.ImageDraw,
    rect: tuple[int, int, int, int],
    title: str,
    lines: Iterable[str],
    fill: str = WHITE,
    outline: str = BORDER,
    title_fill: str = NAVY,
) -> None:
    draw.rounded_rectangle(rect, radius=20, fill=fill, outline=outline, width=2)
    x1, y1, x2, _ = rect
    draw.text((x1 + 18, y1 + 14), title, font=FONT_SUBTITLE, fill=title_fill)
    y = y1 + 50
    for line in lines:
        y = draw_multiline(draw, (x1 + 18, y), f"- {line}", FONT_BODY, TEXT, x2 - x1 - 36, 4)


def draw_title(draw: ImageDraw.ImageDraw, title: str, subtitle: str, width: int) -> None:
    draw.text((50, 30), title, font=FONT_TITLE, fill=NAVY)
    draw.text((50, 78), subtitle, font=FONT_BODY, fill=MUTED)
    draw.line((50, 112, width - 50, 112), fill=MID_BLUE, width=2)


def draw_arrow(draw: ImageDraw.ImageDraw, start: tuple[int, int], end: tuple[int, int], fill: str = NAVY, width: int = 3) -> None:
    draw.line((start, end), fill=fill, width=width)
    ex, ey = end
    sx, sy = start
    if abs(ex - sx) >= abs(ey - sy):
        direction = 1 if ex >= sx else -1
        points = [(ex, ey), (ex - 12 * direction, ey - 7), (ex - 12 * direction, ey + 7)]
    else:
        direction = 1 if ey >= sy else -1
        points = [(ex, ey), (ex - 7, ey - 12 * direction), (ex + 7, ey - 12 * direction)]
    draw.polygon(points, fill=fill)


def generate_architecture() -> Path:
    width, height = 1800, 1100
    image = Image.new("RGB", (width, height), BG)
    draw = ImageDraw.Draw(image)
    draw_title(
        draw,
        "Arsitektur Sistem Maharani Mobil App",
        "Rancangan arsitektur web application yang menghubungkan lima aktor dengan aplikasi dan basis data terpusat.",
        width,
    )

    actor_y = 170
    actor_names = ["Pengunjung", "Customer", "Marketing", "Supervisor", "Owner"]
    actor_xs = [120, 120, 120, 120, 120]
    for index, name in enumerate(actor_names):
        top = actor_y + (index * 150)
        rect = (70, top, 300, top + 92)
        draw.rounded_rectangle(rect, radius=22, fill=BLUE, outline=BORDER, width=2)
        draw.text((rect[0] + 32, rect[1] + 30), name, font=FONT_ACTOR, fill=NAVY)

    app_rect = (480, 180, 1180, 860)
    draw.rounded_rectangle(app_rect, radius=28, fill=WHITE, outline=NAVY, width=3)
    draw.text((app_rect[0] + 28, app_rect[1] + 20), "Aplikasi Web Maharani Mobil", font=FONT_TITLE, fill=NAVY)
    app_modules = [
        "Autentikasi dan manajemen role",
        "Katalog, detail mobil, dan favorit",
        "Penawaran harga dan histori negosiasi",
        "Booking test drive dan tindak lanjut",
        "Pemesanan online dan tracking pesanan",
        "Validasi pembayaran dan transaksi showroom",
        "Dokumen digital: faktur, kwitansi, BAST",
        "Review customer, dashboard, dan laporan",
    ]
    y = app_rect[1] + 90
    for module in app_modules:
        module_rect = (app_rect[0] + 24, y, app_rect[2] - 24, y + 68)
        draw.rounded_rectangle(module_rect, radius=18, fill="#F8FBFF", outline=BORDER, width=2)
        draw.text((module_rect[0] + 20, module_rect[1] + 22), module, font=FONT_BODY, fill=TEXT)
        y += 78

    db_rect = (1330, 300, 1700, 740)
    draw.rounded_rectangle(db_rect, radius=28, fill=WHITE, outline="#2E7D68", width=3)
    draw.text((db_rect[0] + 28, db_rect[1] + 20), "Database Sistem", font=FONT_SUBTITLE, fill="#206050")
    db_lines = [
        "users",
        "cars",
        "favorites",
        "offers",
        "offer_histories",
        "test_drives",
        "orders",
        "payments",
        "product_reviews",
    ]
    y = db_rect[1] + 72
    for line in db_lines:
        draw.rounded_rectangle((db_rect[0] + 24, y, db_rect[2] - 24, y + 38), radius=14, fill=GREEN, outline="#B8E3C7")
        draw.text((db_rect[0] + 40, y + 9), line, font=FONT_BODY, fill="#215C43")
        y += 47

    for index in range(len(actor_names)):
        start_y = actor_y + (index * 150) + 46
        draw_arrow(draw, (300, start_y), (480, 240 + (index * 110)))

    draw_arrow(draw, (1180, 520), (1330, 520), fill="#206050")
    draw_arrow(draw, (1330, 560), (1180, 560), fill="#206050")

    out_path = OUTPUT_DIR / "arsitektur-sistem-maharani-mobil.png"
    image.save(out_path)
    return out_path


def generate_usecase() -> Path:
    width, height = 2100, 1250
    image = Image.new("RGB", (width, height), BG)
    draw = ImageDraw.Draw(image)
    draw_title(
        draw,
        "Use Case Diagram Maharani Mobil App",
        "Aktor dan fungsi utama yang aktif pada sistem saat ini.",
        width,
    )

    system_rect = (380, 150, 1760, 1130)
    draw.rounded_rectangle(system_rect, radius=28, outline=NAVY, width=3, fill="#FCFDFF")
    draw.text((system_rect[0] + 24, system_rect[1] + 18), "Sistem Maharani Mobil App", font=FONT_SUBTITLE, fill=NAVY)

    actors = [
        ("Pengunjung", (80, 230)),
        ("Customer", (80, 470)),
        ("Marketing", (80, 760)),
        ("Supervisor", (80, 980)),
        ("Owner", (1835, 390)),
    ]
    for name, (x, y) in actors:
        draw.ellipse((x + 34, y, x + 90, y + 56), outline=NAVY, width=3, fill=WHITE)
        draw.line((x + 62, y + 56, x + 62, y + 118), fill=NAVY, width=3)
        draw.line((x + 20, y + 82, x + 104, y + 82), fill=NAVY, width=3)
        draw.line((x + 62, y + 118, x + 28, y + 168), fill=NAVY, width=3)
        draw.line((x + 62, y + 118, x + 96, y + 168), fill=NAVY, width=3)
        draw.text((x, y + 184), name, font=FONT_ACTOR, fill=NAVY)

    usecases = [
        ((500, 220, 880, 286), "Lihat Beranda, Katalog,\nDetail Mobil, dan Ulasan"),
        ((500, 315, 880, 381), "Login / Register"),
        ((500, 450, 880, 516), "Kelola Favorit"),
        ((920, 450, 1300, 516), "Ajukan Penawaran Harga"),
        ((1340, 450, 1720, 516), "Booking Test Drive"),
        ((500, 545, 880, 611), "Pesan Mobil Online"),
        ((920, 545, 1300, 611), "Lanjutkan Pembayaran"),
        ((1340, 545, 1720, 611), "Lihat Tracking Pesanan"),
        ((500, 640, 880, 706), "Unduh Dokumen Transaksi"),
        ((920, 640, 1300, 706), "Tulis Review Customer"),
        ((500, 760, 880, 826), "Kelola Data Mobil"),
        ((920, 760, 1300, 826), "Pantau Pesanan, Penawaran,\nTest Drive, dan Transaksi"),
        ((1340, 760, 1720, 826), "Pantau Data Customer"),
        ((500, 900, 880, 966), "Kelola Pesanan dan\nInput Transaksi Showroom"),
        ((920, 900, 1300, 966), "Validasi Pembayaran"),
        ((1340, 900, 1720, 966), "Kelola User Internal,\nReview, dan Aktivitas"),
        ((920, 1030, 1300, 1096), "Lihat Dashboard dan\nLaporan Penjualan"),
        ((1340, 1030, 1720, 1096), "Export PDF / Excel"),
    ]

    for rect, label in usecases:
        draw.rounded_rectangle(rect, radius=32, outline=BORDER, width=2, fill=WHITE)
        x1, y1, x2, y2 = rect
        lines = label.split("\n")
        total_h = len(lines) * 22 + (len(lines) - 1) * 4
        start_y = y1 + ((y2 - y1 - total_h) // 2)
        for line in lines:
            bbox = draw.textbbox((0, 0), line, font=FONT_BODY)
            tx = x1 + ((x2 - x1 - (bbox[2] - bbox[0])) // 2)
            draw.text((tx, start_y), line, font=FONT_BODY, fill=TEXT)
            start_y += 26

    connections = [
        ((184, 312), (500, 253)),
        ((184, 312), (500, 348)),
        ((184, 552), (500, 483)),
        ((184, 552), (920, 483)),
        ((184, 552), (1340, 483)),
        ((184, 552), (500, 578)),
        ((184, 552), (920, 578)),
        ((184, 552), (1340, 578)),
        ((184, 552), (500, 673)),
        ((184, 552), (920, 673)),
        ((184, 842), (500, 793)),
        ((184, 842), (920, 793)),
        ((184, 842), (1340, 793)),
        ((184, 1062), (500, 933)),
        ((184, 1062), (920, 933)),
        ((184, 1062), (1340, 933)),
        ((1911, 552), (1720, 1063)),
        ((1911, 552), (1720, 933)),
    ]
    for start, end in connections:
        draw.line((start, end), fill=NAVY, width=3)

    out_path = OUTPUT_DIR / "use-case-diagram-maharani-mobil.png"
    image.save(out_path)
    return out_path


def generate_erd() -> Path:
    width, height = 2100, 1400
    image = Image.new("RGB", (width, height), BG)
    draw = ImageDraw.Draw(image)
    draw_title(
        draw,
        "ERD Maharani Mobil App",
        "Entitas utama dan relasi data yang digunakan sistem saat ini.",
        width,
    )

    entities = {
        "User": ((120, 180, 560, 460), ["id", "name", "email", "phone", "password", "role", "provider", "provider_id"]),
        "Car": ((790, 180, 1230, 490), ["id", "kode_unit", "merk", "tipe", "tahun", "harga", "kilometer", "status", "photos", "created_by"]),
        "Favorite": ((1450, 180, 1870, 360), ["id", "user_id", "car_id"]),
        "Offer": ((120, 570, 560, 900), ["id", "user_id", "car_id", "offer_price", "counter_price", "final_price", "status", "follow_up_status", "handled_by"]),
        "OfferHistory": ((790, 610, 1230, 860), ["id", "offer_id", "actor_role", "action", "offered_price", "note"]),
        "TestDrive": ((1450, 560, 1870, 900), ["id", "order_id", "user_id", "car_id", "booking_date", "booking_time", "status", "handled_by"]),
        "Order": ((390, 1010, 830, 1340), ["id", "order_code", "user_id", "car_id", "status", "total", "payment_method", "transaction_channel", "document_status"]),
        "Payment": ((960, 1030, 1400, 1290), ["id", "order_id", "method", "amount", "status", "verified_by", "handled_by"]),
        "ProductReview": ((1520, 1000, 1960, 1320), ["id", "user_id", "car_id", "source_type", "source_id", "rating", "review_text", "status"]),
    }

    for name, (rect, fields) in entities.items():
        x1, y1, x2, y2 = rect
        draw.rounded_rectangle(rect, radius=24, fill=WHITE, outline=NAVY, width=3)
        draw.rounded_rectangle((x1, y1, x2, y1 + 50), radius=24, fill=NAVY, outline=NAVY)
        draw.text((x1 + 18, y1 + 12), name, font=FONT_SUBTITLE, fill=WHITE)
        y = y1 + 66
        for field in fields:
            draw.text((x1 + 18, y), field, font=FONT_BODY, fill=TEXT)
            y += 28

    relations = [
        ((560, 260), (790, 260), "1..*"),
        ((1230, 300), (1450, 260), "1..*"),
        ((340, 460), (340, 570), "1..*"),
        ((560, 720), (790, 720), "1..*"),
        ((1670, 360), (1670, 560), "1..*"),
        ((610, 900), (610, 1010), "1..*"),
        ((830, 1140), (960, 1140), "1..1"),
        ((1010, 490), (1010, 610), "1..*"),
        ((1670, 900), (1670, 1000), "1..*"),
    ]
    for start, end, label in relations:
        draw_arrow(draw, start, end, fill=MUTED, width=3)
        mx = (start[0] + end[0]) // 2
        my = (start[1] + end[1]) // 2 - 18
        draw.text((mx, my), label, font=FONT_SMALL_BOLD, fill=MUTED)

    notes = [
        "User dan Car menjadi entitas pusat pada sistem.",
        "Order adalah inti transaksi dan memiliki satu Payment utama.",
        "Offer terhubung ke OfferHistory untuk menyimpan histori negosiasi.",
        "ProductReview dapat berasal dari pembelian maupun test drive.",
    ]
    note_rect = (70, 1210, 350, 1360)
    draw.rounded_rectangle(note_rect, radius=20, fill=BLUE, outline=BORDER, width=2)
    draw.text((note_rect[0] + 16, note_rect[1] + 14), "Catatan Relasi", font=FONT_SUBTITLE, fill=NAVY)
    y = note_rect[1] + 52
    for note in notes:
        y = draw_multiline(draw, (note_rect[0] + 16, y), f"- {note}", FONT_SMALL, TEXT, note_rect[2] - note_rect[0] - 32, 4)

    out_path = OUTPUT_DIR / "erd-maharani-mobil.png"
    image.save(out_path)
    return out_path


def generate_class_diagram() -> Path:
    width, height = 2200, 1450
    image = Image.new("RGB", (width, height), BG)
    draw = ImageDraw.Draw(image)
    draw_title(
        draw,
        "Class Diagram Maharani Mobil App",
        "Struktur kelas inti dan hubungan logika yang aktif pada sistem.",
        width,
    )

    classes = {
        "User": ((70, 180, 520, 540), ["+ id", "+ name", "+ email", "+ phone", "+ role"], ["+ favorites()", "+ orders()", "+ offers()", "+ testDrives()", "+ isCustomer()", "+ isOwner()"]),
        "Car": ((650, 180, 1100, 560), ["+ id", "+ kode_unit", "+ merk", "+ tipe", "+ tahun", "+ harga", "+ status"], ["+ orders()", "+ offers()", "+ testDrives()", "+ productReviews()"]),
        "Favorite": ((1250, 220, 1600, 430), ["+ user_id", "+ car_id"], ["+ user()", "+ car()"]),
        "Offer": ((70, 650, 520, 980), ["+ offer_price", "+ counter_price", "+ final_price", "+ status"], ["+ histories()", "+ negotiatedPrice()", "+ canCheckout()"]),
        "OfferHistory": ((650, 700, 1050, 940), ["+ offer_id", "+ actor_role", "+ action"], ["+ offer()", "+ actionLabel()"]),
        "TestDrive": ((1200, 620, 1650, 990), ["+ booking_date", "+ booking_time", "+ status", "+ customer_channel"], ["+ user()", "+ car()", "+ order()", "+ processOutcomeLabel()"]),
        "Order": ((350, 1060, 820, 1400), ["+ order_code", "+ status", "+ total", "+ payment_method", "+ transaction_channel"], ["+ payment()", "+ issuePaymentDocuments()", "+ issueSettlementDocuments()", "+ isFullyPaid()", "+ areTransactionDocumentsReady()"]),
        "Payment": ((980, 1080, 1380, 1350), ["+ method", "+ amount", "+ status", "+ verified_at"], ["+ order()", "+ verifier()", "+ handledBy()", "+ coversFullAmount()"]),
        "ProductReview": ((1540, 1060, 2050, 1400), ["+ source_type", "+ source_id", "+ rating", "+ review_text", "+ media_paths"], ["+ user()", "+ car()", "+ verifier()", "+ reviewPhotos()"]),
    }

    for name, (rect, attrs, methods) in classes.items():
        x1, y1, x2, y2 = rect
        draw.rounded_rectangle(rect, radius=20, fill=WHITE, outline=NAVY, width=3)
        draw.rectangle((x1, y1, x2, y1 + 46), fill=NAVY)
        draw.text((x1 + 16, y1 + 10), name, font=FONT_SUBTITLE, fill=WHITE)
        split1 = y1 + 46 + (len(attrs) * 24) + 20
        draw.line((x1, split1, x2, split1), fill=BORDER, width=2)
        y = y1 + 60
        for attr in attrs:
            draw.text((x1 + 16, y), attr, font=FONT_BODY, fill=TEXT)
            y += 24
        y = split1 + 12
        for method in methods:
            draw.text((x1 + 16, y), method, font=FONT_BODY, fill=MUTED)
            y += 24

    relation_specs = [
        ((520, 300), (650, 300), "User 1..* Car Interaction"),
        ((1100, 310), (1250, 310), "Car 1..* Favorite"),
        ((295, 540), (295, 650), "User 1..* Offer"),
        ((520, 800), (650, 800), "Offer 1..* OfferHistory"),
        ((1425, 430), (1425, 620), "Car/User -> TestDrive"),
        ((585, 980), (585, 1060), "Offer/TestDrive -> Order"),
        ((820, 1210), (980, 1210), "Order 1..1 Payment"),
        ((1770, 990), (1770, 1060), "Car/User -> Review"),
    ]
    for start, end, label in relation_specs:
        draw_arrow(draw, start, end, fill=MUTED, width=3)
        mx = (start[0] + end[0]) // 2
        my = (start[1] + end[1]) // 2 - 16
        draw.text((mx, my), label, font=FONT_SMALL, fill=MUTED)

    out_path = OUTPUT_DIR / "class-diagram-maharani-mobil.png"
    image.save(out_path)
    return out_path


def main() -> None:
    outputs = [
        generate_architecture(),
        generate_usecase(),
        generate_erd(),
        generate_class_diagram(),
    ]
    for output in outputs:
        print(output)


if __name__ == "__main__":
    main()
