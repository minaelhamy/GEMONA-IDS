from __future__ import annotations

from .amazon_eg import AmazonEgSource
from .carrefour import CarrefourSource
from .elfar import MahmoudElFarSource
from .gourmet import GourmetSource
from .hyperone import HyperOneSource
from .seoudi import SeoudiSource

SOURCES = {
    "amazon_eg": AmazonEgSource,
    "gourmet": GourmetSource,
    "seoudi": SeoudiSource,
    "mahmoud_elfar": MahmoudElFarSource,
    "hyperone": HyperOneSource,
    "carrefour": CarrefourSource,
}
