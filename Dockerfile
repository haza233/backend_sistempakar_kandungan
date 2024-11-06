# Gunakan image dasar yang sesuai
FROM nixos/nix

# Set working directory
WORKDIR /app

# Salin file .nixpacks ke dalam image
COPY .nixpacks/nixpkgs-5148520bfab61f99fd25fb9ff7bfbb50dad3c9db.nix .nixpacks/nixpkgs-5148520bfab61f99fd25fb9ff7bfbb50dad3c9db.nix

# Jalankan perintah nix-env untuk menginstall dependensi
RUN nix-env -if .nixpacks/nixpkgs-5148520bfab61f99fd25fb9ff7bfbb50dad3c9db.nix || { echo 'nix-env failed'; exit 1; } \
    && nix-collect-garbage -d

# Salin asset ke dalam image
COPY .nixpacks/assets /assets/

# Perintah selanjutnya, misalnya untuk menjalankan aplikasi
# CMD ["your_command_here"]
