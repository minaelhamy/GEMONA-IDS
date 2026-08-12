from __future__ import annotations

from .amazon_eg import AmazonEgSource
from .btech import BtechSource
from .carrefour import CarrefourSource
from .elfar import MahmoudElFarSource
from .gourmet import GourmetSource
from .hyperone import HyperOneSource
from .seoudi import SeoudiSource

SOURCES = {
    "amazon_eg": AmazonEgSource,
    "btech": BtechSource,
    "gourmet": GourmetSource,
    "seoudi": SeoudiSource,
    "mahmoud_elfar": MahmoudElFarSource,
    "hyperone": HyperOneSource,
    "carrefour": CarrefourSource,
}
