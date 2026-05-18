#!/usr/bin/env python3
"""Regenerate MAS_AuthHub banner and icons. Requires Pillow."""

from __future__ import annotations

import os
import sys

from PIL import Image, ImageDraw, ImageFont

ACCENT = (79, 70, 229, 255)
ACCENT_DARK = (49, 46, 129, 255)
WHITE = (255, 255, 255, 255)
GOLD = (250, 204, 21, 255)


def lerp(a: float, b: float, t: float) -> float:
    return a + (b - a) * t


def horizontal_gradient(size: tuple[int, int], left: tuple, right: tuple) -> Image.Image:
    w, h = size
    img = Image.new("RGBA", size)
    pix = img.load()
    for x in range(w):
        t = x / max(w - 1, 1)
        r = int(lerp(left[0], right[0], t))
        g = int(lerp(left[1], right[1], t))
        b = int(lerp(left[2], right[2], t))
        al = int(lerp(left[3], right[3], t))
        for y in range(h):
            pix[x, y] = (r, g, b, al)
    return img


def load_font(size: int) -> ImageFont.FreeTypeFont | ImageFont.ImageFont:
    for p in (
        "/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf",
    ):
        if os.path.isfile(p):
            return ImageFont.truetype(p, size)
    return ImageFont.load_default()


def draw_shield_key(img: Image.Image, cx: float, cy: float, scale: float) -> None:
    dr = ImageDraw.Draw(img)
    s = scale
    shield = [
        (cx, cy - 110 * s),
        (cx + 78 * s, cy - 70 * s),
        (cx + 78 * s, cy + 15 * s),
        (cx, cy + 95 * s),
        (cx - 78 * s, cy + 15 * s),
        (cx - 78 * s, cy - 70 * s),
    ]
    dr.polygon(shield, fill=WHITE)
    inner = [
        (cx, cy - 78 * s),
        (cx + 52 * s, cy - 48 * s),
        (cx + 52 * s, cy + 8 * s),
        (cx, cy + 62 * s),
        (cx - 52 * s, cy + 8 * s),
        (cx - 52 * s, cy - 48 * s),
    ]
    dr.polygon(inner, fill=ACCENT)
    kr = 18 * s
    dr.ellipse([cx - kr, cy - 8 * s - kr, cx + kr, cy - 8 * s + kr], fill=WHITE)
    slot_w = 10 * s
    dr.rectangle([cx - slot_w, cy + 2 * s, cx + slot_w, cy + 42 * s], fill=WHITE)
    dr.ellipse(
        [cx + 38 * s - 12 * s, cy + 18 * s - 12 * s, cx + 38 * s + 12 * s, cy + 18 * s + 12 * s],
        fill=GOLD,
    )


def make_banner(out_path: str) -> None:
    w, h = 600, 120
    base = horizontal_gradient((w, h), ACCENT_DARK, ACCENT)
    dr = ImageDraw.Draw(base)
    dr.text((24, 26), "MAS AuthHub", font=load_font(28), fill=WHITE)
    dr.text(
        (24, 66),
        "OAuth2, OIDC, SAML, WebAuthn, and MAMS SSO",
        font=load_font(13),
        fill=(220, 218, 255, 255),
    )
    overlay = Image.new("RGBA", (w, h), (0, 0, 0, 0))
    draw_shield_key(overlay, 518, 72, 0.20)
    Image.alpha_composite(base, overlay).save(out_path, "PNG")


def make_icon(size: int, out_path: str) -> None:
    margin = int(size * 0.06)
    grad = horizontal_gradient((size, size), ACCENT_DARK, ACCENT)
    mask = Image.new("L", (size, size), 0)
    mdr = ImageDraw.Draw(mask)
    mdr.rounded_rectangle(
        [margin, margin, size - margin, size - margin],
        radius=int(size * 0.18),
        fill=255,
    )
    img = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    img.paste(grad, (0, 0))
    img.putalpha(mask)
    layer = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    draw_shield_key(layer, size / 2, size / 2, size / 512)
    img = Image.alpha_composite(img, layer)
    out = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    out.paste(img, (0, 0))
    out.putalpha(mask)
    out.save(out_path, "PNG")


def make_icon_gif(out_path: str, size: int = 32) -> None:
    img = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    grad = horizontal_gradient((size, size), ACCENT_DARK, ACCENT)
    img.paste(grad, (0, 0))
    layer = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    draw_shield_key(layer, size / 2, size / 2, size / 512)
    img = Image.alpha_composite(img, layer)
    rgb = Image.new("RGB", img.size, (255, 255, 255))
    rgb.paste(img, mask=img.split()[3])
    rgb.save(out_path, "GIF")


def main() -> int:
    root = os.path.dirname(os.path.abspath(__file__))
    images = os.path.normpath(os.path.join(root, "..", "images"))
    if not os.path.isdir(images):
        print("Missing images directory:", images, file=sys.stderr)
        return 1
    make_banner(os.path.join(images, "banner.png"))
    make_icon(192, os.path.join(images, "icon-192.png"))
    make_icon(512, os.path.join(images, "icon-512.png"))
    make_icon_gif(os.path.join(images, "icon.gif"), 32)
    print("OK ->", images)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
