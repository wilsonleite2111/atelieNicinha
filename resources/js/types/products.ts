export type ProductImage = {
    id: number;
    url: string;
};

export type Product = {
    id: number;
    name: string;
    description: string | null;
    size: string | null;
    price: string;
    sku: string | null;
    active: boolean;
    thumbnail?: string;
    images?: ProductImage[];
};
