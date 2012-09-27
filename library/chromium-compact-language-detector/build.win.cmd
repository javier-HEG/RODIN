set CFLAGS=-I. -O2 -DCLD_WINDOWS -DWIN32
set LDFLAGS=-L.
set CC=g++
set AR=ar

del *.obj
del libcld.lib

set SOURCES=encodings/compact_lang_det/cldutil.cc encodings/compact_lang_det/cldutil_dbg_empty.cc encodings/compact_lang_det/compact_lang_det.cc encodings/compact_lang_det/compact_lang_det_impl.cc encodings/compact_lang_det/ext_lang_enc.cc encodings/compact_lang_det/getonescriptspan.cc encodings/compact_lang_det/letterscript_enum.cc encodings/compact_lang_det/tote.cc encodings/compact_lang_det/generated/cld_generated_score_quadchrome_0406.cc encodings/compact_lang_det/generated/compact_lang_det_generated_cjkbis_0.cc encodings/compact_lang_det/generated/compact_lang_det_generated_ctjkvz.cc encodings/compact_lang_det/generated/compact_lang_det_generated_deltaoctachrome.cc encodings/compact_lang_det/generated/compact_lang_det_generated_quadschrome.cc encodings/compact_lang_det/win/cld_htmlutils_windows.cc encodings/compact_lang_det/win/cld_unilib_windows.cc encodings/compact_lang_det/win/cld_utf8statetable.cc encodings/compact_lang_det/win/cld_utf8utils_windows.cc encodings/internal/encodings.cc languages/internal/languages.cc

echo ""
echo "Compiling..."

%CC% -c %CFLAGS% %SOURCES%
%AR% rcs libcld.a *.o
%CC% %CFLAGS% %LFLAGS% -o cld.exe rodin.cc libcld.a
del *.o
del libcld.a

echo "Finished! Please move 'cld.exe' to your CGI-BIN folder!"