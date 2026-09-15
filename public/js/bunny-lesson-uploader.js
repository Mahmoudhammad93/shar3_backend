(() => {
  const MAX_BYTES = 8 * 1024 * 1024 * 1024;
  const ALLOWED_EXT = [".mp4", ".webm", ".mov"];

  function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || "";
  }

  async function request(url, options = {}) {
    const response = await fetch(url, {
      credentials: "same-origin",
      ...options,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf(),
        "X-Requested-With": "XMLHttpRequest",
        ...(options.headers || {}),
      },
    });

    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      throw new Error(data.message || "تعذّر إكمال الطلب");
    }

    return data;
  }

  function uploadBinary(url, file, onProgress) {
    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();
      xhr.open("PUT", url);
      xhr.withCredentials = true;
      xhr.setRequestHeader("Accept", "application/json");
      xhr.setRequestHeader("Content-Type", "application/octet-stream");
      xhr.setRequestHeader("X-CSRF-TOKEN", csrf());
      xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
      xhr.upload.onprogress = (event) => {
        if (event.lengthComputable && onProgress) {
          onProgress(Math.round((event.loaded / event.total) * 100));
        }
      };
      xhr.onload = () => {
        let data = {};
        try {
          data = JSON.parse(xhr.responseText || "{}");
        } catch (error) {
          data = {};
        }
        if (xhr.status >= 200 && xhr.status < 300) {
          resolve(data);
          return;
        }
        reject(new Error(data.message || "تعذّر رفع الملف"));
      };
      xhr.onerror = () => reject(new Error("تعذّر رفع الملف"));
      xhr.send(file);
    });
  }

  function extensionAllowed(name) {
    const lower = (name || "").toLowerCase();
    return ALLOWED_EXT.some((ext) => lower.endsWith(ext));
  }

  function register() {
    if (!window.Alpine || window.__share3aBunnyUploader) {
      return;
    }

    window.__share3aBunnyUploader = true;

    window.Alpine.data("bunnyLessonUploader", (config) => ({
      lessonId: config.lessonId || null,
      formStatePath: config.formStatePath || "data",
      videoId: config.videoId || "",
      statusLabel: config.statusLabel || "لم يتم الرفع بعد",
      uploading: false,
      progress: 0,
      message: "",
      fileName: "",

      field(name) {
        return `${this.formStatePath}.${name}`;
      },

      async syncForm(status) {
        if (!this.$wire) {
          return;
        }

        await this.$wire.set(this.field("bunny_video_id"), this.videoId || null);
        await this.$wire.set(this.field("bunny_library_id"), config.libraryId || null);
        await this.$wire.set(this.field("bunny_status"), status);
        await this.$wire.set(this.field("video_provider"), this.videoId ? "bunny" : null);
      },

      titleFromForm() {
        return this.$wire?.get(this.field("title_ar")) || this.fileName || "درس";
      },

      async onFileSelected(event) {
        const file = event.target.files?.[0];
        event.target.value = "";
        if (!file || this.uploading) {
          return;
        }

        if (!extensionAllowed(file.name)) {
          this.message = "الصيغ المسموحة: MP4 و WebM و MOV فقط.";
          return;
        }

        if (file.size > MAX_BYTES) {
          this.message = "حجم الملف أكبر من 8 غيغابايت.";
          return;
        }

        this.fileName = file.name;
        this.uploading = true;
        this.progress = 0;
        this.message = "";

        const previousVideoId = this.videoId;
        let createdVideoId = null;

        try {
          const startUrl = previousVideoId && this.lessonId
            ? `/admin/lessons/${this.lessonId}/bunny/replace`
            : "/admin/bunny/uploads";

          const started = await request(startUrl, {
            method: "POST",
            body: JSON.stringify({ title: this.titleFromForm() }),
          });

          createdVideoId = started.video_id;
          if (!createdVideoId) {
            throw new Error("تعذّر تجهيز الرفع.");
          }

          await uploadBinary(
            `/admin/bunny/uploads/${createdVideoId}/content`,
            file,
            (percent) => {
              this.progress = percent;
            },
          );

          if (this.lessonId) {
            const confirmed = await request(`/admin/lessons/${this.lessonId}/bunny/confirm`, {
              method: "POST",
              body: JSON.stringify({ video_id: createdVideoId }),
            });
            this.videoId = confirmed.video_id;
            this.statusLabel = confirmed.status_label || "قيد المعالجة";
            await this.syncForm(confirmed.bunny_status || "processing");
          } else {
            this.videoId = createdVideoId;
            this.statusLabel = "قيد المعالجة";
            if (started.upload?.library_id) {
              config.libraryId = started.upload.library_id;
            }
            await this.syncForm("processing");
          }

          this.message = this.lessonId
            ? "تم رفع الفيديو إلى Bunny. الحالة الآن قيد المعالجة."
            : "تم رفع الفيديو إلى Bunny. احفظ الدرس لربط الفيديو به.";
        } catch (error) {
          this.statusLabel = previousVideoId ? this.statusLabel : "فشل";
          this.message = error?.message || "فشل رفع الفيديو.";

          if (createdVideoId && createdVideoId !== previousVideoId) {
            await request("/admin/bunny/uploads/cancel", {
              method: "POST",
              body: JSON.stringify({ video_id: createdVideoId }),
            }).catch(() => {});
          }
        } finally {
          this.uploading = false;
        }
      },

      async refreshStatus() {
        if (!this.lessonId || !this.videoId) {
          this.statusLabel = "لم يتم الرفع بعد";
          return;
        }

        const data = await request(`/admin/lessons/${this.lessonId}/bunny/status`);
        this.statusLabel = data.status_label || data.bunny_status || this.statusLabel;
        if (data.bunny_status) {
          await this.syncForm(data.bunny_status);
        }
      },

      async removeVideo() {
        if (this.lessonId) {
          this.message = "الفيديو المحفوظ لن يُحذف تلقائياً حتى لا تُفقد الوسائط الحالية.";
          return;
        }

        if (!this.videoId) {
          return;
        }

        await request("/admin/bunny/uploads/cancel", {
          method: "POST",
          body: JSON.stringify({ video_id: this.videoId }),
        }).catch(() => {});

        this.videoId = "";
        this.fileName = "";
        this.statusLabel = "لم يتم الرفع بعد";
        await this.syncForm(null);
        this.message = "تم إلغاء الفيديو غير المحفوظ.";
      },
    }));
  }

  document.addEventListener("alpine:init", register);
  if (window.Alpine) {
    register();
  }
})();
