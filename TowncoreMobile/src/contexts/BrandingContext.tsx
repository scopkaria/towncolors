import React, { createContext, useContext, useEffect, useState } from 'react';
import { brandingApi, MobileBrandingResponse } from '../api';
import { APP_NAME } from '../config';

type BrandingState = {
  appName: string;
  colors: MobileBrandingResponse['colors'] | null;
  logoUrl: string | null;
  appIconUrl: string | null;
  loaded: boolean;
};

const defaultBranding: BrandingState = {
  appName: APP_NAME,
  colors: null,
  logoUrl: null,
  appIconUrl: null,
  loaded: false,
};

const BrandingContext = createContext<BrandingState>(defaultBranding);

export function BrandingProvider({ children }: { children: React.ReactNode }) {
  const [branding, setBranding] = useState<BrandingState>(defaultBranding);

  useEffect(() => {
    let mounted = true;

    async function loadBranding() {
      try {
        const data = await brandingApi.get();
        if (!mounted) return;

        setBranding({
          appName: data.app_name || APP_NAME,
          colors: data.colors,
          logoUrl: data.assets?.logo_url || null,
          appIconUrl: data.assets?.app_icon_url || null,
          loaded: true,
        });
      } catch {
        if (!mounted) return;
        setBranding((prev) => ({ ...prev, loaded: true }));
      }
    }

    loadBranding();

    return () => {
      mounted = false;
    };
  }, []);

  return <BrandingContext.Provider value={branding}>{children}</BrandingContext.Provider>;
}

export function useBranding() {
  return useContext(BrandingContext);
}
