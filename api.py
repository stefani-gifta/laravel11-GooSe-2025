from flask import Flask, request, jsonify
from transformers import AutoTokenizer, AutoModelForSeq2SeqLM
import torch

app = Flask(__name__)

# --- KONFIGURASI ---
# Pastikan nama folder ini SAMA PERSIS dengan folder hasil training di notebook kamu
MODEL_DIR = "flan-t5-ai-simple-bot" 

print("Sedang memuat model AI... Mohon tunggu sebentar...")

# Cek apakah folder model ada, kalau belum training, pakai model bawaan Google dulu
try:
    tokenizer = AutoTokenizer.from_pretrained(MODEL_DIR)
    model = AutoModelForSeq2SeqLM.from_pretrained(MODEL_DIR)
    print("Model hasil training berhasil dimuat!")
except:
    print(f"Folder {MODEL_DIR} tidak ditemukan. Menggunakan model default 'google/flan-t5-base'")
    MODEL_DIR = "google/flan-t5-base"
    tokenizer = AutoTokenizer.from_pretrained(MODEL_DIR)
    model = AutoModelForSeq2SeqLM.from_pretrained(MODEL_DIR)

@app.route('/predict', methods=['POST'])
def predict():
    # 1. Terima data
    data = request.json
    user_input = data.get('message', '')
    
    # --- TAMBAHAN DEBUGGING (CEK INI) ---
    print(f"\n[MASUK] Pesan dari User: {user_input}") 
    # ------------------------------------

    if not user_input:
        return jsonify({"reply": "Maaf, saya tidak menerima pesan."})

    # 2. Proses AI
    prompt = f"Explain this AI concept with an analogy: {user_input}"
    
    inputs = tokenizer(
        prompt,
        return_tensors="pt",
        truncation=True,
        max_length=256
    )

    output_ids = model.generate(
        **inputs,
        max_new_tokens=128,
        num_beams=5,
        temperature=0.7,
        early_stopping=True
    )

    bot_reply = tokenizer.decode(output_ids[0], skip_special_tokens=True)

    # --- TAMBAHAN DEBUGGING (CEK INI) ---
    print(f"[KELUAR] Jawaban AI: {bot_reply}\n")
    # ------------------------------------
    
    return jsonify({"reply": bot_reply})

if __name__ == '__main__':
    # Jalankan di port 5000
    app.run(host='127.0.0.1', port=5000, debug=True)