from __future__ import annotations

from .carrefour import CarrefourSource
from .elfar import MahmoudElFarSource
from .gourmet import GourmetSource
from .hyperone import HyperOneSource
from .seoudi import SeoudiSource

SOURCES = {
    "gourmet": GourmetSource,
    "seoudi": SeoudiSource,
    "mahmoud_elfar": MahmoudElFarSource,
    "hyperone": HyperOneSource,
    "carrefour": CarrefourSource,
}
