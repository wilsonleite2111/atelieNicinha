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
    active: boolean;
    category_id: number | null;
    thumbnail?: string;
    images?: ProductImage[];
};
