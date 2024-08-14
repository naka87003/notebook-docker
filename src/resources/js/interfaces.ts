export type Note = {
  id: number;
  title?: string;
  created_at: string;
  updated_at: string;
  starts_at?: string;
  ends_at?: string;
  description?: string;
  public: boolean;
  status: {
    name: string;
  };
  tag?: {
    name?: string;
    hex_color?: string;
  };
  category: {
    id: number;
    vuetify_theme_color_name?: string;
    mdi_name?: string;
  }
};