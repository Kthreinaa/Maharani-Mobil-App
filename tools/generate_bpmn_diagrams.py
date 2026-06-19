from pathlib import Path
from PIL import Image, ImageDraw, ImageFont


ROOT = Path(__file__).resolve().parents[1]
OUTPUT_DIR = ROOT / "docs" / "bpmn"
FONT_REGULAR = Path(r"C:\Windows\Fonts\arial.ttf")
FONT_BOLD = Path(r"C:\Windows\Fonts\arialbd.ttf")


def font(size: int, bold: bool = False) -> ImageFont.FreeTypeFont:
    """Ambil font Windows yang umum agar teks diagram tetap rapi."""
    return ImageFont.truetype(str(FONT_BOLD if bold else FONT_REGULAR), size)


def text_size(draw: ImageDraw.ImageDraw, text: str, text_font) -> tuple[int, int]:
    box = draw.multiline_textbbox((0, 0), text, font=text_font, spacing=4, align="center")
    return box[2] - box[0], box[3] - box[1]


def draw_centered_text(draw: ImageDraw.ImageDraw, box: tuple[int, int, int, int], text: str, text_font, fill: str) -> None:
    width, height = text_size(draw, text, text_font)
    x1, y1, x2, y2 = box
    draw.multiline_text(
        ((x1 + x2 - width) / 2, (y1 + y2 - height) / 2),
        text,
        font=text_font,
        fill=fill,
        spacing=4,
        align="center",
    )


def draw_rounded_box(draw: ImageDraw.ImageDraw, box, text: str, fill="#F8FBFF", outline="#5EA4D8") -> None:
    """Gambar task BPMN sederhana dengan kotak rounded."""
    draw.rounded_rectangle(box, radius=18, fill=fill, outline=outline, width=3)
    draw_centered_text(draw, box, text, font(28, bold=False), "#183A59")


def draw_gateway(draw: ImageDraw.ImageDraw, center: tuple[int, int], text: str) -> None:
    """Gambar gateway keputusan berbentuk diamond."""
    cx, cy = center
    size = 70
    points = [(cx, cy - size), (cx + size, cy), (cx, cy + size), (cx - size, cy)]
    draw.polygon(points, fill="#FFF7E8", outline="#D9911B")
    draw_centered_text(draw, (cx - 48, cy - 34, cx + 48, cy + 34), text, font(21, bold=True), "#9A5A00")


def draw_circle(draw: ImageDraw.ImageDraw, center: tuple[int, int], radius: int, fill: str, outline: str, width: int = 4) -> None:
    cx, cy = center
    draw.ellipse((cx - radius, cy - radius, cx + radius, cy + radius), fill=fill, outline=outline, width=width)


def draw_end_event(draw: ImageDraw.ImageDraw, center: tuple[int, int]) -> None:
    draw_circle(draw, center, 22, "#FFFFFF", "#D33F49", 4)
    draw_circle(draw, center, 15, "#FFFFFF", "#D33F49", 4)


def draw_arrow(draw: ImageDraw.ImageDraw, start: tuple[int, int], end: tuple[int, int], fill="#3F5F7A", width: int = 4) -> None:
    """Gambar panah lurus atau siku sederhana agar alur mudah dibaca."""
    x1, y1 = start
    x2, y2 = end

    if y1 == y2 or x1 == x2:
        draw.line((x1, y1, x2, y2), fill=fill, width=width)
    else:
        mid_x = x2 if abs(x2 - x1) < 120 else (x1 + x2) // 2
        draw.line((x1, y1, mid_x, y1), fill=fill, width=width)
        draw.line((mid_x, y1, mid_x, y2), fill=fill, width=width)
        draw.line((mid_x, y2, x2, y2), fill=fill, width=width)

    arrow = 16
    if abs(x2 - x1) >= abs(y2 - y1):
        points = [(x2, y2), (x2 - arrow, y2 - 8), (x2 - arrow, y2 + 8)]
    else:
        direction = 1 if y2 > y1 else -1
        points = [(x2, y2), (x2 - 8, y2 - arrow * direction), (x2 + 8, y2 - arrow * direction)]
    draw.polygon(points, fill=fill)


def draw_lane_label(image: Image.Image, box, text: str, fill: str = "#123B62") -> None:
    """Teks lane diputar vertikal agar mirip contoh Bizagi."""
    x1, y1, x2, y2 = box
    label = Image.new("RGBA", (y2 - y1, x2 - x1), (0, 0, 0, 0))
    label_draw = ImageDraw.Draw(label)
    label_draw.rectangle((0, 0, label.width, label.height), fill="#D9E8F6")
    draw_centered_text(label_draw, (0, 0, label.width, label.height), text, font(26, bold=True), fill)
    rotated = label.rotate(90, expand=True)
    image.paste(rotated, (x1, y1), rotated)


def create_canvas(title: str, lane_names: list[str], lane_height: int, width: int, height: int):
    image = Image.new("RGB", (width, height), "#FFFFFF")
    draw = ImageDraw.Draw(image)

    draw.rounded_rectangle((18, 18, width - 18, height - 18), radius=24, fill="#E8F2FC", outline="#6DA5D4", width=4)
    draw.text((width / 2, 42), title, anchor="mm", font=font(34, bold=True), fill="#0A2540")

    pool_x1, pool_y1 = 70, 95
    pool_x2, pool_y2 = width - 40, height - 45
    label_w = 90

    draw.rectangle((pool_x1, pool_y1, pool_x2, pool_y2), fill="#F8FBFF", outline="#6DA5D4", width=3)

    for index, lane_name in enumerate(lane_names):
        lane_top = pool_y1 + index * lane_height
        lane_bottom = lane_top + lane_height
        if index > 0:
            draw.line((pool_x1, lane_top, pool_x2, lane_top), fill="#86B6DD", width=2)
        draw.rectangle((pool_x1, lane_top, pool_x1 + label_w, lane_bottom), fill="#D7E8F8", outline="#86B6DD", width=2)
        draw_lane_label(image, (pool_x1, lane_top, pool_x1 + label_w, lane_bottom), lane_name)

    return image, draw, (pool_x1 + label_w + 28, pool_y1, pool_x2 - 24, pool_y2)


def generate_penjualan() -> Path:
    image, draw, work = create_canvas(
        "BPMN Penjualan Setelah Menggunakan Sistem Maharani Mobil App",
        ["Customer", "Sistem Maharani Mobil App", "Supervisor"],
        lane_height=250,
        width=2450,
        height=930,
    )
    x1, y1, x2, y2 = work
    customer_y = y1 + 125
    system_y = y1 + 250 + 125
    supervisor_y = y1 + 500 + 125

    start = (x1 + 20, customer_y)
    draw_circle(draw, start, 18, "#B8F299", "#4C9A2A")

    task_w = 250
    task_h = 92

    boxes = {
        "akses": (x1 + 90, customer_y - 46, x1 + 90 + task_w, customer_y + 46),
        "login": (x1 + 390, customer_y - 46, x1 + 390 + task_w, customer_y + 46),
        "lihat": (x1 + 690, customer_y - 46, x1 + 690 + task_w, customer_y + 46),
        "pesan": (x1 + 1010, customer_y - 46, x1 + 1010 + task_w, customer_y + 46),
        "simpan": (x1 + 1010, system_y - 46, x1 + 1010 + task_w, system_y + 46),
        "review": (x1 + 1325, supervisor_y - 46, x1 + 1325 + task_w, supervisor_y + 46),
        "instruksi": (x1 + 1325, system_y - 46, x1 + 1325 + task_w, system_y + 46),
        "bayar": (x1 + 1640, customer_y - 46, x1 + 1640 + task_w, customer_y + 46),
        "validasi": (x1 + 1640, supervisor_y - 46, x1 + 1640 + task_w, supervisor_y + 46),
        "update": (x1 + 1955, system_y - 46, x1 + 1955 + task_w, system_y + 46),
        "serah": (x1 + 1955, supervisor_y - 46, x1 + 1955 + task_w, supervisor_y + 46),
        "terima": (x1 + 1955, customer_y - 46, x1 + 1955 + task_w, customer_y + 46),
    }

    draw_rounded_box(draw, boxes["akses"], "Akses website /\naplikasi")
    draw_rounded_box(draw, boxes["login"], "Login /\nRegister")
    draw_rounded_box(draw, boxes["lihat"], "Lihat katalog\n& detail mobil")
    draw_rounded_box(draw, boxes["pesan"], "Pilih unit dan\nkirim pesanan")
    draw_rounded_box(draw, boxes["simpan"], "Sistem menyimpan\norder dan metode beli")
    draw_gateway(draw, (x1 + 1320, system_y - 95), "Cash\n/\nKredit")
    draw_rounded_box(draw, boxes["review"], "Supervisor meninjau\npesanan / pengajuan")
    draw_rounded_box(draw, boxes["instruksi"], "Sistem menampilkan\ninstruksi pembayaran /\nstatus pengajuan")
    draw_rounded_box(draw, boxes["bayar"], "Upload bukti bayar /\nkirim pengajuan kredit")
    draw_rounded_box(draw, boxes["validasi"], "Supervisor memvalidasi\npembayaran / DP")
    draw_rounded_box(draw, boxes["update"], "Sistem update status\npesanan dan dokumen")
    draw_rounded_box(draw, boxes["serah"], "Supervisor finalisasi\nserah terima unit")
    draw_rounded_box(draw, boxes["terima"], "Customer menerima\nunit dan dokumen")

    note_box = (x1 + 1260, system_y + 52, x1 + 1550, system_y + 132)
    draw.rounded_rectangle(note_box, radius=14, fill="#FFF7E8", outline="#D9A441", width=2)
    draw_centered_text(draw, note_box, "Metode beli: Cash / Kredit\nMetode bayar: Tunai / Transfer", font(20, bold=False), "#8A5A00")

    end = (x2 - 10, customer_y)
    draw_end_event(draw, end)

    draw_arrow(draw, (start[0] + 18, start[1]), (boxes["akses"][0], customer_y))
    draw_arrow(draw, (boxes["akses"][2], customer_y), (boxes["login"][0], customer_y))
    draw_arrow(draw, (boxes["login"][2], customer_y), (boxes["lihat"][0], customer_y))
    draw_arrow(draw, (boxes["lihat"][2], customer_y), (boxes["pesan"][0], customer_y))
    draw_arrow(draw, (boxes["pesan"][2], customer_y), (boxes["simpan"][0], system_y))
    draw_arrow(draw, (boxes["simpan"][2], system_y), (boxes["review"][0], supervisor_y))
    draw_arrow(draw, (boxes["review"][2], supervisor_y), (boxes["instruksi"][0], system_y))
    draw_arrow(draw, (boxes["instruksi"][2], system_y), (boxes["bayar"][0], customer_y))
    draw_arrow(draw, (boxes["bayar"][2], customer_y), (boxes["validasi"][0], supervisor_y))
    draw_arrow(draw, (boxes["validasi"][2], supervisor_y), (boxes["update"][0], system_y))
    draw_arrow(draw, (boxes["update"][2], system_y), (boxes["serah"][0], supervisor_y))
    draw_arrow(draw, (boxes["serah"][2], supervisor_y), (boxes["terima"][0], customer_y))
    draw_arrow(draw, (boxes["terima"][2], customer_y), (end[0] - 22, customer_y))

    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    out = OUTPUT_DIR / "bpmn-penjualan-setelah-sistem.png"
    image.save(out)
    return out


def generate_rekapitulasi() -> Path:
    image, draw, work = create_canvas(
        "BPMN Rekapitulasi Penjualan Setelah Menggunakan Sistem Maharani Mobil App",
        ["Marketing", "Supervisor", "Sistem Maharani Mobil App", "Owner"],
        lane_height=205,
        width=2450,
        height=980,
    )
    x1, y1, x2, y2 = work
    marketing_y = y1 + 102
    supervisor_y = y1 + 205 + 102
    system_y = y1 + 410 + 102
    owner_y = y1 + 615 + 102

    start = (x1 + 20, marketing_y)
    draw_circle(draw, start, 18, "#B8F299", "#4C9A2A")
    end = (x2 - 10, owner_y)
    draw_end_event(draw, end)

    task_w = 280
    task_h = 92

    boxes = {
        "input": (x1 + 90, marketing_y - 46, x1 + 90 + task_w, marketing_y + 46),
        "validasi": (x1 + 420, supervisor_y - 46, x1 + 420 + task_w, supervisor_y + 46),
        "simpan": (x1 + 770, system_y - 46, x1 + 770 + task_w, system_y + 46),
        "lengkapi": (x1 + 1120, supervisor_y - 46, x1 + 1120 + task_w, supervisor_y + 46),
        "olah": (x1 + 1470, system_y - 46, x1 + 1470 + task_w, system_y + 46),
        "pilih": (x1 + 1800, owner_y - 46, x1 + 1800 + task_w, owner_y + 46),
        "tampil": (x1 + 1800, system_y - 46, x1 + 1800 + task_w, system_y + 46),
        "export": (x1 + 1800, owner_y + 86, x1 + 1800 + task_w, owner_y + 178),
    }

    draw_rounded_box(draw, boxes["input"], "Marketing input / update\ndata produk dan aktivitas\npenjualan")
    draw_rounded_box(draw, boxes["validasi"], "Supervisor validasi\npesanan, transaksi,\ndan pembayaran")
    draw_rounded_box(draw, boxes["simpan"], "Sistem menyimpan data\norder, pembayaran,\nstatus, dan dokumen")
    draw_rounded_box(draw, boxes["lengkapi"], "Supervisor melengkapi\nstatus transaksi sampai\nsiap direkap")
    draw_rounded_box(draw, boxes["olah"], "Sistem mengolah data\npenjualan per periode\nharian / bulanan / tahunan")
    draw_rounded_box(draw, boxes["pilih"], "Owner memilih periode\nlaporan penjualan")
    draw_rounded_box(draw, boxes["tampil"], "Sistem menampilkan\ndashboard, grafik,\ndan rekap penjualan")
    draw_rounded_box(draw, boxes["export"], "Owner export laporan\nPDF / Excel")

    draw_arrow(draw, (start[0] + 18, start[1]), (boxes["input"][0], marketing_y))
    draw_arrow(draw, (boxes["input"][2], marketing_y), (boxes["validasi"][0], supervisor_y))
    draw_arrow(draw, (boxes["validasi"][2], supervisor_y), (boxes["simpan"][0], system_y))
    draw_arrow(draw, (boxes["simpan"][2], system_y), (boxes["lengkapi"][0], supervisor_y))
    draw_arrow(draw, (boxes["lengkapi"][2], supervisor_y), (boxes["olah"][0], system_y))
    draw_arrow(draw, (boxes["olah"][2], system_y), (boxes["pilih"][0], owner_y))
    draw_arrow(draw, (boxes["pilih"][0] + 140, owner_y - 46), (boxes["tampil"][0] + 140, system_y + 46))
    draw_arrow(draw, (boxes["tampil"][2], system_y), (boxes["export"][0], owner_y + 132))
    draw_arrow(draw, (boxes["export"][2], owner_y + 132), (end[0] - 22, owner_y))

    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    out = OUTPUT_DIR / "bpmn-rekapitulasi-penjualan-setelah-sistem.png"
    image.save(out)
    return out


def main() -> None:
    penjualan = generate_penjualan()
    rekap = generate_rekapitulasi()
    print(penjualan)
    print(rekap)


if __name__ == "__main__":
    main()
