<?php

$sidebarItems = [
    'EXAMPLE' => [
        [
            'label' => 'Menu',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'pi-home',
                    'url' => 'dashboard',
                ],
                [
                    'label' => 'Testing',
                    'icon' => 'pi-unlock',
                    'url' => 'testing'
                ],
                [
                    'label' => 'Data Produk',
                    'icon' => 'pi-box',
                    'items' => [
                        [
                            'label' => 'Produksi',
                            'icon' => 'pi-box',
                            'url' => 'testing'
                        ]
                    ]
                ],
                [
                    'label' => 'Data Testing',
                    'icon' => 'pi-box',
                    'items' => [
                        [
                            'label' => 'Testing pertama',
                            'icon' => 'pi-box',
                            'items' => [
                                [
                                    'label' => 'Testing kedua 1',
                                    'icon' => 'pi-box',
                                    'url' => 'dashboard',
                                ],
                                [
                                    'label' => 'Testing kedua 2',
                                    'icon' => 'pi-box',
                                    'url' => 'testing',
                                ],
                                [
                                    'label' => 'Testing kedua 3',
                                    'icon' => 'pi-box',
                                    'url' => 'settings.profile',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
        [
            'label' => "KONTOL",
            'items' => []
        ]
    ],
    'guest' => [
        [
            'label' => 'Menu',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'pi-objects-column',
                    'url' => 'dashboard',
                ],
            ],
        ]
    ],
    'staf produksi' => [
        [
            'label' => 'Main',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'pi-objects-column',
                    'url' => 'dashboard',
                ],
            ],
        ],
        [
            'label' => 'Management',
            'items' => [
                [
                    'label' => 'Produksi',
                    'icon' => 'pi-cog',
                    'url' => 'stafProduksi.produksi',
                ],
            ],
        ],
    ],
    'staf admin' => [
        [
            'label' => 'Main',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'pi-objects-column',
                    'url' => 'dashboard',
                ],
                [
                    'label' => 'Traceability',
                    'icon' => 'pi-search',
                    'url' => 'auth.trace',
                ],
            ],
        ],
        [
            'label' => 'Management',
            'items' => [
                [
                    'label' => 'Bahan Baku',
                    'icon' => 'pi-inbox',
                    'items' => [
                        [
                            'label' => 'Restok Bahan Baku',
                            'icon' => 'pi-sync',
                            'url' => 'stafAdmin.restok_bahan_baku',
                        ],
                        [
                            'label' => 'Lot Bahan Baku',
                            'icon' => 'pi-tags',
                            'url' => 'stafAdmin.lot_bahan_baku',
                        ],
                    ]
                ],
                [
                    'label' => 'Distribusi Produk',
                    'icon' => 'pi-send',
                    'url' => 'stafAdmin.distribusi',
                ],
                // [
                //     'label' => 'Retur Produk',
                //     'icon' => 'pi-file',
                //     'url' => 'stafAdmin.retur_produk',
                // ],
                [
                    'label' => 'Recall Produk',
                    'icon' => 'pi-exclamation-triangle',
                    'url' => 'stafAdmin.recall_produk',
                ],
            ],
        ],
    ],
    'owner' => [
        [
            'label' => 'Main',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'pi-objects-column',
                    'url' => 'dashboard',
                ],
                [
                    'label' => 'Traceability',
                    'icon' => 'pi-search',
                    'url' => 'auth.trace',
                ],
                [
                    'label' => 'Laporan',
                    'icon' => 'pi-file',
                    'url' => 'owner.laporan',
                ],
                [
                    'label' => 'Biodata UMKM',
                    'icon' => 'pi-briefcase',
                    'url' => 'owner.biodata_umkm',
                ],
            ],
        ],
        [
            'label' => 'Management',
            'items' => [
                [
                    'label' => 'Data Master',
                    'icon' => 'pi-database',
                    'items' => [
                        [
                            'label' => 'Produk',
                            'icon' => 'pi-list',
                            'url' => 'stafAdmin.master_produk',
                        ],
                        [
                            'label' => 'Sertifikasi',
                            'icon' => 'pi-check-square',
                            'url' => 'stafAdmin.master_sertifikasi',
                        ],
                        [
                            'label' => 'Kategori',
                            'icon' => 'pi-tags',
                            'url' => 'owner.master_kategori',
                        ],
                        [
                            'label' => 'Supplier',
                            'icon' => 'pi-briefcase',
                            'url' => 'stafAdmin.master_supplier',
                        ],
                        [
                            'label' => 'Bahan Baku',
                            'icon' => 'pi-inbox',
                            'url' => 'stafAdmin.master_bahan_baku',
                        ],
                        [
                            'label' => 'Lokasi Penyimpanan',
                            'icon' => 'pi-map-marker',
                            'url' => 'owner.lokasi_penyimpanan',
                        ],
                    ]
                ],
                [
                    'label' => 'Kelola Data User',
                    'icon' => 'pi-lock',
                    'url' => 'owner.kelola_user',
                ],
            ],
        ],
        
    ]
];

return $sidebarItems;