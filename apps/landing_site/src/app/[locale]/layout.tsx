import type { Metadata } from "next";
import { Inter, Outfit } from "next/font/google";
import "./globals.css";

const inter = Inter({
  variable: "--font-sans",
  subsets: ["latin"],
});

const outfit = Outfit({
  variable: "--font-display",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  title: "WADEXP — Real-Time Mobility & Logistics Excellence",
  description: "Request a ride, send a package, and experience the future of mobility with WADEXP. Ghana's premier enterprise-grade logistics platform.",
};

export default function LocaleLayout({
  children,
  params,
}: {
  children: React.ReactNode;
  params: { locale: string };
}) {
  return (
    <html lang={params.locale} className={`${inter.variable} ${outfit.variable} antialiased`}>
      <body className="min-h-screen bg-white text-zinc-900 selection:bg-blue-600 selection:text-white">
        {children}
      </body>
    </html>
  );
}

export async function generateStaticParams() {
  return [{ locale: 'en' }, { locale: 'fr' }, { locale: 'es' }];
}
